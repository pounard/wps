<?php

namespace Wps\Media\Type;

use Smvc\Plugin\DefaultFactory;

class TypeFactory extends DefaultFactory
{
    public function __construct()
    {
        $this->registerAll(array(
            'image/gif' => '\Wps\Media\Type\ImageType',
            'image/jpeg' => '\Wps\Media\Type\ImageType',
            'image/png' => '\Wps\Media\Type\ImageType',
            'image/svg+xml' => '\Wps\Media\Type\ImageType',
            'image/vnd.microsoft.icon' => '\Wps\Media\Type\ImageType',
        ));
    }
}
