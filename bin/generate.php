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

/*
 * Ugly as hell but this'll work
 */

$options = getopt("u:a:l:o:");

if (isset($options['l'])) {
  if (!is_numeric($options['l']) || $options['l'] < 0) {
    print "Invalid limit given\n";
    exit(1);
  } else {
    $limit = (int)$options['l'];
    print "User set limit: " . $limit . "\n";
  }
} else {
  $limit = 1000;
  print "Using default limit: " . $limit . "\n";
}
$offset = 0;

$app = $dispatcher->getApplication();
$config = $app->getConfig();
$db = $app->getDatabase();
$mediaDao = $app->getDao('media');
$toolkit = new ExternalImagickImageToolkit();

$st = $db->prepare("
    SELECT m.id
    FROM media m
    ORDER BY m.id ASC
    LIMIT " . $limit . " OFFSET " . $offset . "
");
$st->setFetchMode(\PDO::FETCH_COLUMN, 0);
$st->execute();
$idList = $st->fetchAll();

print "Found " . count($idList) . " medias to generate\n";

$sizes = array('300', 'w300', 'w600', 'w900', 'w1200');

foreach ($idList as $id) {
    $media = $mediaDao->load($id);

    if (!$media instanceof Media) {
        print " !! Skipping invalid media with id " . $id . "\n";
        continue;
    }

    $path = $media->getRealPath();

    print "Proceeding with media " . $id . "\n";
    print " * Path is " . $path . "\n";

    $inFile = FileSystem::pathJoin($config['directory/public'], 'full', $path);
    if (!file_exists($inFile)) {
        print " !! Original file does not exist, skipping media\n";
    }

    print " *";

    foreach ($sizes as $size) {
        $outFile = FileSystem::pathJoin($config['directory/public'], $size, $path);

        if (file_exists($outFile)) {
            print " [" . $size . "]";
            continue;
        }

        print " " . $size;

        if ('h' === $size[0]) {
            $mode = 'h';
            $size = substr($size, 1);
        } else if ('w' === $size[0]) {
            $mode = 'w';
            $size = substr($size, 1);
        } else if ('m' === $size[0]) {
            $mode = 'm';
            $size = substr($size, 1);
        } else {
            $mode = 's'; // Square
            $size = $size;
        }

        switch ($mode) {

            case 'm':
                $toolkit->scaleTo($inFile, $outFile, $size, $size, true);
                break;

            case 'h':
                $toolkit->scaleTo($inFile, $outFile, null, $size, true);
                break;

            case 'w':
                $toolkit->scaleTo($inFile, $outFile, $size, null, true);
                break;

            case 's':
                $toolkit->scaleAndCrop($inFile, $outFile, $size, $size);
                break;
        }
    }

    print "\n";
}
