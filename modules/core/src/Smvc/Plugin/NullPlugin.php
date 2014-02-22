<?php

namespace Smvc\Plugin;

class NullPlugin
{
    public function __call()
    {
        return null;
    }

    public function __invoke()
    {
        return null;
    }
}
