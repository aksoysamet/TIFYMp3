<?php
/*include 'YouTubeDownloader.php';
$yt = new YouTubeDownloader();
var_dump($yt->getDownloadLinks("7wtfhZwyrcc"));*/

$json = file_get_contents('https://www.shazam.com/discovery/v2/tr/TR/web/-/search?searchQuery=taio%20cruz%20there%20she%20goes&pageSize=1&startFrom=0');
$djson = json_decode($json, true);
echo $djson['tracks']['hits'][0]['images']['default'];

?>