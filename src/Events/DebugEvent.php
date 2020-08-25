<?php

namespace Mailamie\Events;

class DebugEvent implements Event
{
    public string $param;

    public function __construct(string $param)
    {
        $this->param = $param;
    }
}
