<?php
$header = array();

if (isset($_POST['media-id'])) {
    array_push($header, 'id');
}

if (isset($_POST['media-title'])) {
    array_push($header, 'title');
}

if (isset($_POST['media-description'])) {
    array_push($header, 'description');
}

if (isset($_POST['media-url'])) {
    array_push($header, 'url');
}

if (isset($_POST['media-thumbnail'])) {
    array_push($header, 'thumbnail');
}

if (isset($_POST['media-type'])) {
    array_push($header, 'media type');
}

if (isset($_POST['media-user'])) {
    array_push($header, 'creator');
}

if (isset($_POST['media-tags'])) {
    array_push($header, 'tags');
}

if (isset($_POST['media-lastplayed'])) {
    array_push($header, 'last played');
}

if (isset($_POST['media-lastupdated'])) {
    array_push($header, 'last updated');
}

if (isset($_POST['media-duration'])) {
    array_push($header, 'duration');
}

if (isset($_POST['media-originalname'])) {
    array_push($header, 'original file name');
}

if (isset($_POST['media-categories'])) {
    array_push($header, 'categories');
}

$category_name = $_POST["category-name"];
$page = $_POST["page"];

require_once('config.php');

require_once('php5/KalturaClient.php');
$config = new KalturaConfiguration($partnerId);
$config->serviceUrl = 'http://www.kaltura.com';
$client = new KalturaClient($config);
$ks = $client->generateSession($adminSecret, $userId, KalturaSessionType::ADMIN, $partnerId);
$client->setKs($ks);
$filter = new KalturaMediaEntryFilter();
$pager = new KalturaFilterPager();
$pager->pageSize = 500;
$pager->pageIndex = $page;
$filter->categoriesMatchOr = $category_name;
$filteredListResult = $client->media->listAction($filter, $pager);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="kaltura-export.csv"');

$export_csv[0] = $header;

foreach ($filteredListResult->objects as $entry) {
        //$imageLink = '<img src="http://cdn.kaltura.com/p/'.$partnerId.'/thumbnail/entry_id/'.$entry->id.'/width/50/height/50/type/1/quality/100" />';        
        $mediaType = $entry->mediaType;
        $user = $entry->creatorId;
        $tags = $entry->tags;
        $lastPlayed = gmdate("m.d.y", $entry->lastPlayedAt);
        $lastUpdated = gmdate("m.d.y", $entry->updatedAt);
        $duration = $entry->duration;
        $originalName = $entry->partnerData;
        $categories = $entry->categories;

        $thumbnail = $entry->thumbnailUrl;
        $id = $entry->id;
        $title = $entry->name;
        $description = $entry->description;
        $url = $entry->downloadUrl;
        
        $items = array();
        if (isset($_POST['media-id'])) {
            array_push($items, $id);
        }

        if (isset($_POST['media-title'])) {
            array_push($items, $title);
        }

        if (isset($_POST['media-description'])) {
            array_push($items, $description);
        }

        if (isset($_POST['media-url'])) {
            array_push($items, $url);
        }

        if (isset($_POST['media-thumbnail'])) {
            array_push($items, $thumbnail);
        }

        if (isset($_POST['media-type'])) {
            array_push($items, $mediaType);
        }

        if (isset($_POST['media-user'])) {
            array_push($items, $user);
        }

        if (isset($_POST['media-tags'])) {
            array_push($items, $tags);
        }

        if (isset($_POST['media-lastplayed'])) {
            array_push($items, $lastPlayed);
        }

        if (isset($_POST['media-lastupdated'])) {
            array_push($items, $lastUpdated);
        }

        if (isset($_POST['media-duration'])) {
            array_push($items, $duration);
        }

        if (isset($_POST['media-originalname'])) {
            array_push($items, $originalName);
        }

        if (isset($_POST['media-categories'])) {
            array_push($items, $categories);
        }

        $export_csv[] = $items;
}

$fp = fopen('php://output', 'wb');
foreach ($export_csv as $line) {
    fputcsv($fp, $line, ',');
}

fclose($fp);

?>