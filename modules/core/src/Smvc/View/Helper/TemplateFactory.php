<?php

namespace Smvc\View\Helper;

use Smvc\Plugin\DefaultFactory;

class TemplateFactory extends DefaultFactory
{
    public function __construct()
    {
        $this->registerAll(array(
            'esc'      => '\Smvc\View\Helper\Template\Esc',
            'messages' => '\Smvc\View\Helper\Template\Messages',
            'null'     => '\Smvc\View\Helper\Template\NullHelper',
            'pager'    => '\Smvc\View\Helper\Template\Pager',
            'url'      => '\Smvc\View\Helper\Template\Url',
        ));
    }
}
