<?php declare(strict_types=1);

namespace Mailamie\Events;

class DebugEvent implements Event
{
    public string $param;

    public function __construct(string $param)
    {
        $this->param = $param;
    }
}
