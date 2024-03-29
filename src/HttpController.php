<?php declare(strict_types=1);

namespace Mailamie;

use Mailamie\Emails\Store;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;

class HttpController
{
    private Store $store;
    // @phpstan-ignore-next-line > Used by index.php
    private string $version;
    // @phpstan-ignore-next-line > Used by index.php
    private string $websocketHost;

    public function __construct(Store $store, string $websocketHost)
    {
        $this->store = $store;
        $this->version = Config::VERSION;
        $this->websocketHost = $websocketHost;
    }

    public function route(ServerRequestInterface $request): Response
    {
        try {
            $path = $request->getUri()->getPath();

            if (preg_match('/^\/api\//i', $path)) {
                return $this->handleApiCall($request);
            }

            $path = $this->convertToPublicPath($path);

            if (file_exists($path)) {
                if (self::endsWith($path, '.php')) {
                    return $this->handlePhpFile($path);
                }

                return $this->handleStaticFile($path);
            }
        } catch (Throwable $e) {
            return $this->serverError($e);
        }

        return $this->fileNotFoundError();
    }

    private function convertToPublicPath(string $originalPath): string
    {
        $path = $originalPath === '/' ? '/index.php' : $originalPath;

        return __DIR__ . "/../public" . str_replace("..", "", $path);
    }

    private function handleApiCall(ServerRequestInterface $request): Response
    {
        $path = $request->getUri()->getPath();

        try {
            if (preg_match('/^\/api\/messages\/?$/i', $path)) {
                return $this->json($this->store->all());
            }

            if (preg_match('/^\/api\/messages\/([^\/]*)$/i', $path, $matches)) {
                $id = (string)$matches[1];
                $message = $this->store->get($id);

                return $this->json($message->toArray());
            }

            if (preg_match('/^\/api\/messages\/([^\/]*)\/attachments\/([^\/]*)$/i', $path, $matches)) {
                $messageId = (string)$matches[1];
                $attachmentId = (string)$matches[2];
                $message = $this->store->get($messageId);
                $attachment = $message->getAttachment($attachmentId);

                return $this->download(
                    $attachment->getFilename(),
                    $attachment->getContent()
                );
            }
        } catch (Throwable $e) {
            return $this->serverError($e);
        }

        return $this->fileNotFoundError();
    }

    private function handleStaticFile(string $path): Response
    {
        return new Response(
            200,
            [
                'Content-Type'  => $this->getMimeType($path),
                'Cache-Control' => 'public, max-age=604800, immutable'
            ],
            file_get_contents($path)
        );
    }

    private function getMimeType(string $path): string
    {
        $mimeTypes = [
            'css' => 'text/css',
            'js'  => 'text/javascript',
            'svg' => 'image/svg+xml',
        ];

        foreach ($mimeTypes as $ext => $type) {
            if (self::endsWith($path, ".{$ext}")) {
                return $type;
            }
        }

        return mime_content_type($path);
    }

    private function handlePhpFile(string $path): Response
    {
        ob_start();
        require($path);
        $page = ob_get_contents();
        ob_end_clean();

        return new Response(
            200,
            ['Content-Type' => 'text/html'],
            $page
        );
    }

    private function fileNotFoundError(): Response
    {
        return new Response(
            404,
            ['Content-Type' => 'text/html'],
            "<h2>404 - Page or content not found</h2>"
        );
    }

    private function serverError(Throwable $e): Response
    {
        return new Response(
            500,
            ['Content-Type' => 'text/html'],
            "<h2>{$e->getMessage()}</h2> <pre>{$e->getTraceAsString()}</pre>"
        );
    }

    /**
     * @param array<int|string,mixed>|object $data
     * @return Response
     */
    private function json($data): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($data)
        );
    }

    private static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }

    private function download(string $filename, string $content): Response
    {
        return new Response(
            200,
            [
                'Content-Type'        => 'application/force-download',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ],
            $content
        );
    }
}
