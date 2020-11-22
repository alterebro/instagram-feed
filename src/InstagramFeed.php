<?php

namespace Alterebro;

class InstagramFeed {

    private $host = "https://www.instagram.com/";
    private $url;
    private $queryString;
    private $query;
    private $cacheFile;
    private $error = false;

    public function __construct($query, $cachePath, $feedItems = 6, $cacheTime = 86400, $cacheForce = false) {

        $this->query = ($query[0] == '@') ? '@' : '#';
        $this->queryString = ($query[0] == '@') ? substr($query, 1) : $query;
        $this->feedItems = $feedItems;
        $this->cachePath = $cachePath;
        $this->cacheTime = $cacheTime;
        $this->cacheFile = $this->cachePath . $this->query . $this->queryString . '.json';
        $this->cacheForce = $cacheForce;

        $this->url = ( $this->query == '@' )
            ? $this->host . $this->queryString . '/'
            : $this->host . 'explore/tags/' . $this->queryString . '/';
    }

    private function extractData($input) {

        $feed = [];

        if ( !substr_count($input, "window._sharedData = ") ) {
            $this->error = true;
            return $feed;
        }

        $output = explode("window._sharedData = ", $input);
        $output = explode(";</script>", $output[1]);
        $output = trim($output[0]);
        $output = json_decode($output, true);

        if ( $this->query == '#' ) { $els = $output['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges']; }
        if ( $this->query == '@' ) { $els = $output['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges']; }

        foreach ($els as $el) {
            $_el = $el['node'];
            $feed[] = [
                'image' => $_el['display_url'],
                'link' =>  $this->host . 'p/' . $_el['shortcode'] . '/',
                'timestamp' => $_el['taken_at_timestamp'],
                'thumb' => $_el['thumbnail_resources'][count($_el['thumbnail_resources'])-1]['src'],
                'caption' => (isset($_el['edge_media_to_caption']['edges'][0]['node']['text'])) ? $_el['edge_media_to_caption']['edges'][0]['node']['text'] : '',
                'alt' => (isset($_el['accessibility_caption'])) ? $_el['accessibility_caption'] : ''
            ];
        }
        $feed = array_slice($feed, 0, $this->feedItems);
        $feed = json_encode($feed);

        return $feed;
    }

    private function getRemoteData() {

        // $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
        // $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1';
        // $agent = 'curl/7.24.0 (x86_64-apple-darwin12.0) libcurl/7.24.0 OpenSSL/0.9.8r zlib/1.2.5';
        // $agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
        $agent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        $data = curl_exec($ch);
        curl_close($ch);

        return $this->extractData($data);
    }

    private function readCache() {
        return file_get_contents($this->cacheFile);
    }

    private function writeCache() {
        $file = file_put_contents(
            $this->cacheFile,
            $this->getRemoteData()
        );
        return $this->readCache();
    }

    private function cacheExists() {
        $file = $this->cacheFile;
        $exists = file_exists($file);
        $notEmpty = @filesize($file) > 10;
        return ($exists && $notEmpty);
    }

    private function cacheNeedsRenewal() {
        return (($this->cacheTime + filemtime($this->cacheFile)) < time());
    }

    public function load() {

        if ( $this->error ) return '[{}]';

        return ( !$this->cacheExists() || $this->cacheForce )
            ? $this->writeCache()
            : ( $this->cacheNeedsRenewal() )
                ? $this->writeCache()
                : $this->readCache();
    }

    public function JSON() {

        header('Content-type:application/json;charset=utf-8');
        echo $this->load();
    }
}
