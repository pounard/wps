<?php

namespace Smvc\View\Helper\Template;

class Url extends AbstractHelper
{
    public function __invoke($path = null, array $args = null)
    {
        if (!empty($args)) {
            $suffix = array();
            foreach ($args as $key => $value) {
                $suffix[] = urlencode($key) . "=" . urlencode($value);
            }
            $suffix = "?" . implode("?", $suffix);
        } else {
            $suffix = '';
        }

        // FIXME: Base path here
        return '/' . urlencode($path) . $suffix;
    }
}
