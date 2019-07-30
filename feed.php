<?php

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/src/InstagramFeed/InstagramFeed.php');

use Alterebro\InstagramFeed\InstagramFeed;

$feed = new InstagramFeed('@alterebro');
$feed->JSON();
