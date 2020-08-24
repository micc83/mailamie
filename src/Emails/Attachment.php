<?php

namespace Mailamie\Emails;

class Attachment
{
    public string $filename;
    public string $content;
    public string $type;
    public string $id;

    public function __construct(string $filename, string $content, string $type)
    {
        $this->id = (string)uniqid();
        $this->filename = $filename;
        $this->content = $content;
        $this->type = $type;
    }
}
