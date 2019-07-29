<?php

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/instagram-feed.class.php');

$feed = new InstagramFeed('@alterebro');
$feed = $feed->load();

header('Content-type:application/json;charset=utf-8');
echo $feed;
?>
