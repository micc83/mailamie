<?php

namespace Mailamie\Events;

class DebugEvent
{
    public $param;

    public function __construct($param)
    {
        $this->param = $param;
    }
}
