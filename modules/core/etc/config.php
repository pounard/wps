<?php
return array(
    // Main configuration
    'config' => array(
        'debug' => false,
        'html' => array(
            // Your site title
            'title' => "Core framework",
        ),
        // Internal charset this software works from: you should never
        // change this; But hey, do what the heck you want! I'm not your
        // mother...
        'charset' => "UTF-8",
        // Default timezone if there is no user override
        'timezone' => 'Europe/Paris',
        // Output filtering configuration, you should not modify
        // this in most cases, defaults are fine for basic usage
        'filters' => array(
            'html' => array('strip', 'lntohr', 'autop', 'urltoa'),
            'plain' => array('htmlesc', 'lntohr', 'autop', 'urltoa'),
            'secure' => array('strip'),
        ),
    ),
    // Loaded modules
    'modules' => array(
        'core' => '\Smvc',
    ),
    // Maybe you want to override those but if you are not
    // a developer please don't
    'services' => array(
        'factory.filter' => '\Smvc\View\Helper\FilterFactory',
        'factory.template' => '\Smvc\View\Helper\TemplateFactory',
        'messager' => '\Smvc\Core\Messager',
    ),
    // Just remove the 'redis' part to disable caching
    // Note: this is a very bad idea
    'redis' => array(
        'host' => 'localhost',
        'port' => null,
    ),
);