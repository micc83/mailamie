<?php

namespace Mailamie;

use Mailamie\Emails\Store;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;

class WebController
{
    private Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
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
                if (static::endsWith($path, '.php')) {
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
        return "public" . $path;
    }

    private function handleApiCall(ServerRequestInterface $request): Response
    {
        $path = $request->getUri()->getPath();

        try {
            if (preg_match('/^\/api\/messages\/?$/i', $path)) {
                return $this->json($this->store->all());
            }

            if (preg_match('/^\/api\/messages\/(.*)$/i', $path, $matches)) {
                $id = (string)$matches[1];
                $message = $this->store->get($id);
                return $this->json($message->toArray());
            }
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    private function handleStaticFile(string $path)
    {
        return new Response(
            200,
            ['Content-Type' => $this->getMimeType($path)],
            file_get_contents($path)
        );
    }

    private function getMimeType($path): string
    {
        $mimeTypes = [
            'css' => 'text/css',
            'js'  => 'text/javascript'
        ];

        foreach ($mimeTypes as $ext => $type) {
            if (static::endsWith($path, ".{$ext}")) {
                return $type;
            }
        }

        return mime_content_type($path);
    }

    private function handlePhpFile(string $path)
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
     * @param array|object $data
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
}