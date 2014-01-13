<?php
return array(
    // Main configuration
    'config' => array(
        // Directories can be either absolute or relative from the
        // public web root, directories must be writable by the CLI
        // script and the PHP user
        'directory' => array(
            // Data where the computed photos will be stored
            'public' => 'public/media',
            // The same folder by relative to webroot if you choose to
            // store it elsewhere
            'web' => 'media',
            // In this folder each user will receive its own folder
            // using his account identifier as name
            'upload' => 'data/upload',
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
        // Default index page
        'index' => 'app/albums',
        // Valid file sizes, "full" means original size has will have
        // a special casing, hence its non apparition in this list 
        'size' => array(
            'thumbnail' => '100',
            'album' => '200',
            'medium' => '600',
            'large' => '900',
            'huge' => '1200',
        ),
    ),
    // Loaded applications
    'applications' => array(
        'account' => '\Account',
        'wps' => '\Wps',
    ),
    'security' => array(
        'accountprovider' => '\Wps\Security\DatabaseAccountProvider',
    ),
    // Maybe you want to override those but if you are not
    // a developer please don't
    'services' => array(
        'dao.album' => '\Wps\Media\Persistence\AlbumDao',
        'dao.media' => '\Wps\Media\Persistence\MediaDao',
        'factory.filter' => '\Smvc\View\Helper\FilterFactory',
        'factory.template' => '\Smvc\View\Helper\TemplateFactory',
        'factory.type' => '\Wps\Media\Type\TypeFactory',
        'messager' => '\Smvc\Core\Messager',
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