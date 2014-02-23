<?php

namespace Smvc\Plugin;

class NullPlugin
{
    public function __call($method, $args)
    {
        return null;
    }

    public function __invoke()
    {
        return null;
    }
}
