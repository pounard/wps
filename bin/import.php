#!/bin/env php
<?php

use Smvc\Core\Bootstrap;
use Smvc\Dispatch\Dispatcher;
use Smvc\Dispatch\Http\HttpRequest;

// Prepare minimal environement
chdir(dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';

// This where magic will happen
$request = HttpRequest::createFromGlobals();
$dispatcher = new Dispatcher();

$config = require_once __DIR__ . '/../etc/config.php';
Bootstrap::bootstrap($dispatcher, $config);
$dispatcher->dispatch($request);
