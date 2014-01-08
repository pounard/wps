<?php
return array(
    // Main configuration
    'config' => array(
        // Directories can be either absolute or relative from the
        // public web root, directories must be writable by the CLI
        // script and the PHP user
        'directory' => array(
            // Data where the computed photos will be stored
            'datadir' => 'data',
            // Original photo copy directory
            'original' => '../data/upload',
            // In this folder each user will receive its own folder
            // using his account identifier as name
            'upload' => '../data/upload',
        ),
        'debug' => false,
        'html' => array(
            // Your site title
            'title' => "Web Photo Sync",
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
            'html2sum' => array('strip', 'lntovd', 'urltoa'),
            'plain' => array('htmlesc', 'lntohr', 'autop', 'urltoa'),
            'plain2sum' => array('htmlesc', 'lntovd', 'urltoa'),
            'secure' => array('strip'),
        ),
    ),
    // Loaded applications
    'applications' => array(
        'wps' => '\\Wps',
    ),
    // Maybe you want to override those but if you are not
    // a developer please don't
    'services' => array(
        'auth' => '\Wps\Security\DatabaseAccountProvider',
        'filterfactory' => '\Smvc\View\Helper\FilterFactory',
        'messager' => '\Smvc\Core\Messager',
        'session' => '\Smvc\Core\Session',
        'templatefactory' => '\Smvc\View\Helper\TemplateFactory',
    ),
    // Just remove the 'redis' part to disable caching
    // Note: this is a very bad idea
    'redis' => array(
        'host' => 'localhost',
        'port' => null,
    ),
    'db' => array(
        'default' => array(
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'username' => 'wps',
            'password' => 'wps',
            'database' => 'wps',
        ),
    ),
);