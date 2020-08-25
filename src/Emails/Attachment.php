<?php declare(strict_types=1);

namespace Mailamie\Emails;

class Attachment
{
    private string $filename;
    private string $content;
    private string $type;
    private string $id;

    public function __construct(string $filename, string $content, string $type)
    {
        $this->id = (string)uniqid();
        $this->filename = $filename;
        $this->content = $content;
        $this->type = $type;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
