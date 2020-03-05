<?php

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/../src/InstagramFeed/InstagramFeed.php');

use Alterebro\InstagramFeed\InstagramFeed;

$httpReferer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : false;
$httpHost = parse_url($httpReferer, PHP_URL_HOST);
$allowedHosts = array(
    "localhost",
    "instagram-feed.alterebro.now.sh",
    "cdpn.io"
);
$queryParam = '@alterebro';
if ( (isset($_GET['q'])) && !empty($_GET['q']) && in_array($httpHost, $allowedHosts)) {

    $queryURL = $_GET['q'];
    preg_match('/^@?[\w\-\_\.]+$/', $queryURL, $matches);
    if ( count($matches) ) { $queryParam = $matches[0]; }
}

header("Access-Control-Allow-Origin: *");
$feed = new InstagramFeed($queryParam);
$feed->JSON();
