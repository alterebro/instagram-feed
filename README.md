# instagram-feed

> Get the latest pictures from an Instagram user or hashtag without OAuth credentials or access token. This is not using the Instagram API platform or Graph API so it doesn't need you to authenticate, authorise or register your application anywhere.

## Installation

    composer require alterebro/instagram-feed


### How to use it

```php
require __DIR__ . '/vendor/autoload.php';

use Alterebro\InstagramFeed;

$feed = new InstagramFeed(
    $query,     // i.e: "@alterebro"
    $cachePath  // i.e: __DIR__ . "/tmp/"
);
$feed->JSON();
```

#### Parameters

— **Mandatory** :

- `$query` : String starting with '@' to retrieve an username or without it to get a hashtag
- `$cachePath` : Folder where to store the data. i.e: `/tmp/` ( `chmod 777 /tmp` )

— **Optional**

- `$feedItems` : Items to retrieve. Defaults to 6 items.
- `$cacheTime` : How long does the cache lasts. Defaults to 86400 seconds (_1 day_).
- `$cacheForce` : Force to cache the data. Defaults to `false`.

```php
// Retrive 10 items from user @alterebro and stores it on the /tmp/ folder for 12 hours / half day (43200 seconds)
$feed = new InstagramFeed('@alterebro', '/tmp/', 10, 43200);

// Get the data :
$data = $feed->load();

// or Output as JSON :
$feed->JSON();
```
