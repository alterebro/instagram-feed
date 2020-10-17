<?php

ob_start();
require_once('feed.php');
$instagramFeed = ob_get_clean();
$instagramFeed = json_decode($instagramFeed, true);
$instagramFeed = array_slice($instagramFeed, 0, 10);

function instaFormat($str) {
    $length = 220;
    $end = '...';
    $output = (strlen($str) > $length)
        ? substr($str, 0, $length - strlen($end)) . $end
        : $str;

    $output = nl2br($output);
    // $output = preg_replace('/\n/i', ' ', $output);
    $output = preg_replace('/(#\w+)/i', '<mark title="${1}">${1}</mark>', $output);
    $output = preg_replace('/(@(\w+))/i', '<a href="https://www.instagram.com/${2}/" title="${1}" target="_blank">${1}</a>', $output);

    return $output;
}

function formatDate($value) {
    return date('m/d/Y, h:i A T', $value);
}

header('Content-Type: text/html; charset=utf-8');

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Instagram Feed</title>
    <meta name="author" content="Jorge Moreno aka moro, moro.es (@alterebro)" />
    <meta name="google" content="notranslate" />
    <link rel="me" href="https://twitter.com/alterebro" />
    <link rel="stylesheet" href="example.css" />
</head>
<body>

<div id="app">
    <ul>
        <?php foreach ($instagramFeed as $item): ?>
        <li>
            <figure>
                <a href="<?php echo $item['link'] ?>" target="_blank"><img src="<?php echo $item['thumb'] ?>" alt="<?php echo $item['alt'] ?>" /></a>
                <figcaption>
                    <h3><a href="<?php echo $item['link'] ?>" target="_blank"><?php echo formatDate($item['timestamp']) ?></a></h3>
                    <p><?php echo instaFormat($item['caption']) ?></p>
                    <p><a href="<?php echo $item['link'] ?>" target="_blank"><?php echo $item['link'] ?></a></p>
                </figcaption>
            </figure>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
