<?php

require_once( __DIR__ . '/../../src/InstagramFeed/InstagramFeed.php');

use InstagramFeed\InstagramFeed;

$queryParam = '@alterebro'; // Default value
$cachePath = dirname(__FILE__).'/../tmp/';

    if ( isset($_GET['q']) && !empty($_GET['q']) ) {

        $queryURL = $_GET['q'];
        preg_match('/^@?[\w\-\_\.]+$/', $queryURL, $matches);
        if ( count($matches) ) { $queryParam = $matches[0]; }
    }

header("Access-Control-Allow-Origin: *");
header("Content-type:application/json;charset=utf-8");

$feed = new InstagramFeed($queryParam, $cachePath);
$feed->JSON();
