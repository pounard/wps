<?php

namespace Smvc\View\Helper\Template;

class NullHelper
{
    public function __invoke()
    {
        return "";
    }
}
