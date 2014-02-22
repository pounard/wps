<?php

namespace Smvc\View;

use Smvc\Core\AbstractApplicationAware;

class TemplateResolver extends AbstractApplicationAware
{
    public function findTemplate($name)
    {
        // First attempt with module name
        if ($pos = strpos($name, '/')) {
            $target = substr($name, 0, $pos);
            if ($module = $this->getApplication()->getModule($target)) {
                $path = $module->getPath() . '/views/' . $name . '.phtml';

                // @todo Views should be registered or cached somewhere
                if (file_exists($path)) {
                    return $path;
                }
            }
        }

        // Then try from the application path.
        $path = 'views/' . $name . '.phtml';
        if (file_exists($path)) {
            return $path;
        }
    }
}
