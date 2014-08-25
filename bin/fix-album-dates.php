#!/bin/env php
<?php

use Wps\Media\Media;
use Wps\Media\Toolkit\ExternalImagickImageToolkit;
use Wps\Util\FileSystem;

use Smvc\Core\Bootstrap;
use Smvc\Dispatch\Dispatcher;
use Smvc\Dispatch\Http\HttpRequest;

// Prepare minimal environement
chdir(dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';

$dispatcher = new Dispatcher();

$config = require_once __DIR__ . '/../etc/config.php';
Bootstrap::bootstrap($dispatcher, $config);

$app = $dispatcher->getApplication();
$config = $app->getConfig();
$db = $app->getDatabase();

try {
    $db->beginTransaction();
    $db->exec("
        UPDATE album a SET
            ts_user_date_begin = (
                SELECT MIN(m.ts_user_date)
                FROM media m
                WHERE m.id_album = a.id
            ),
            ts_user_date_end = (
                SELECT MAX(m.ts_user_date)
                FROM media m
                WHERE m.id_album = a.id
            )
    ");
    $db->commit();
} catch (\Exception $e) {
    echo "FATAL: An error happened: ", $e->getMessage();
    exit(1);
}
