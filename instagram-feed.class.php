<?php
class InstagramFeed {

    var $host = "https://www.instagram.com/";
    var $url;
    var $queryString;
    var $query;

    var $feedItems;

    var $cachePath = "/tmp/";
    var $cacheFile;
    var $cacheTime;
    var $cacheForce;

    function __construct($query, $feedItems = 10, $cacheTime = 86400, $cacheForce = false) {

        $this->queryString = substr($query, 1);
        $this->query = $query[0];

        $this->feedItems = $feedItems;

        // TODO : handle error properly
        $allowed_queries = ['@', '#'];
        if( !in_array($this->query, $allowed_queries) ) {
            // return error...
        }
        // ...

        $this->cacheTime = $cacheTime;
        $this->cacheFile = $this->cachePath . $this->query . $this->queryString . '.json';
        $this->cacheForce = $cacheForce;

        // If user ...
        $this->url = $this->host . $this->queryString . '/';

        // TODO : If hashtag ...
    }

    function extractData($input) {

        $feed = [];

        $output = explode("window._sharedData = ", $input);
        $output = explode(";</script>", $output[1]);
        $output = trim($output[0]);
        $output = json_decode($output, true);

        $els = $output['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
        foreach ($els as $el) {
            $_el = $el['node'];
            $feed[] = [
                'image' => $_el['display_url'],
                'link' =>  $this->host . 'p/' . $_el['shortcode'] . '/',
                'timestamp' => $_el['taken_at_timestamp'],
                'thumb' => $_el['thumbnail_resources'][count($_el['thumbnail_resources'])-1]['src'],
                'caption' => $_el['edge_media_to_caption']['edges'][0]['node']['text'],
                'alt' => $_el['accessibility_caption']
            ];
        }
        $feed = array_slice($feed, 0, $this->feedItems);
        $feed = json_encode($feed);

        return $feed;
    }

    function getRemoteData() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $data = curl_exec($ch);
        curl_close($ch);

        return $this->extractData($data);
    }

    function readCache() {
        return file_get_contents($this->cacheFile);
    }

    function writeCache() {
        $file = file_put_contents(
            $this->cacheFile,
            $this->getRemoteData()
        );
        return $this->readCache();
    }

    function cacheExists() {
        $file = $this->cacheFile;
        $exists = file_exists($file);
        $notEmpty = @filesize($file) > 10;
        return ($exists && $notEmpty);
    }

    function cacheNeedsRenewal() {
        return (($this->cacheTime + filemtime($this->cacheFile)) < time());
    }

    function load() {

        return ( !$this->cacheExists() || $this->cacheForce )
            ? $this->writeCache()
            : ( $this->cacheNeedsRenewal() )
                ? $this->writeCache()
                : $this->readCache();

    }
}

?>
