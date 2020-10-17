<?php

require_once('../src/InstagramFeed/InstagramFeed.php');

use InstagramFeed\InstagramFeed;

$queryParam = '@alterebro';
$cachePath = dirname(__FILE__).'/tmp/';

    if ( isset($_GET['q']) && !empty($_GET['q']) ) {

        $queryURL = $_GET['q'];
        preg_match('/^@?[\w\-\_\.]+$/', $queryURL, $matches);
        if ( count($matches) ) { $queryParam = $matches[0]; }
    }

$feed = new InstagramFeed($queryParam, $cachePath);
header("Access-Control-Allow-Origin: *");
header("Content-type:application/json;charset=utf-8");
echo $feed->load();
