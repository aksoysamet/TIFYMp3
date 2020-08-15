<?php 
require_once '../google-api-php-client/vendor/autoload.php';
$DEVELOPER_KEY = 'AIzaSyAvGsE8x4r9KRv0WMKAptDU9j3mTyDw0ZQ';
$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
$youtube = new Google_Service_YouTube($client);

try {
	$responsedata = array();
	$videos = '';
	getPlaylistItems($_POST['url'], 50, NULL);//'PLlGumB9GJ-FtiRmoyE1UsHpgTd3_OOdma'
	
	$responsedata = shuffle_assoc($responsedata);
	foreach ($responsedata as $key => $video) {
		$pos = getSorterPos($video['title']);
		$videos .= sprintf('%s|%s|%s',
			trim(mb_substr($video['title'], 0, $pos > 40 ? 40 : $pos)),
			$key,
			time_converter($video['duration'])
			);
		$videos .= "\n";
	}
} catch (Google_Service_Exception $e) {
	$videos .= sprintf('A service error occurred: %s',
		htmlspecialchars($e->getMessage()));
} catch (Google_Exception $e) {
	$videos .= sprintf('An client error occurred: %s',
		htmlspecialchars($e->getMessage()));
}
echo str_replace(array('Ğ','Ü','Ş','İ','Ö','Ç','ğ','ü','ş','ı','ö','ç'),array('�','�','�','�','�','�','�','�','�','�','�','�'),$videos);
//echo str_replace(array('Ğ','Ü','Ş','İ','Ö','Ç','ğ','ü','ş','ı','ö','ç'),array('G','U','S','I','O','C','g','u','s','i','o','c'),$videos);

function shuffle_assoc($list) { 
  if (!is_array($list)) return $list; 

  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) { 
    $random[$key] = $list[$key]; 
  }
  return $random; 
} 

function getSorterPos($title) {
	$srtr_pre = array('(', 'Official', 'HD', 'Video');
	$pos = mb_strlen($title);
	foreach($srtr_pre as $find) {
		$tpos = mb_strpos($title,$find);
		if($tpos !== FALSE && $tpos < $pos)
			$pos = $tpos;
	}
	return $pos;
}
function getPlaylistItems($playlistId, $maxResults, $pageToken)
{
	global $youtube;
	global $responsedata;
	global $DEVELOPER_KEY;
	$videolist = '';
	$requestOptions = array(
		'playlistId' => $playlistId,
		'maxResults' => $maxResults,
	);
	if(!empty($pageToken))
		$requestOptions['pageToken'] = $pageToken;
	$response = $youtube->playlistItems->listPlaylistItems('snippet,contentDetails', $requestOptions);
	foreach ($response['items'] as $playlistItem) {
		if(!array_key_exists('thumbnails', $playlistItem['snippet']['modelData']))continue;
		$responsedata[$playlistItem['snippet']['resourceId']['videoId']]['title'] = str_replace('|', '', $playlistItem['snippet']['title']);
		$videolist .= $playlistItem['snippet']['resourceId']['videoId'].',';
	}
	$videolist = rtrim($videolist, ',');
	$link = 'https://www.googleapis.com/youtube/v3/videos?id='.$videolist.'&part=contentDetails&key='.$DEVELOPER_KEY;
	$dresponse = json_decode(file_get_contents($link),true);
	foreach($dresponse['items'] as $item)
		$responsedata[$item['id']]['duration'] = $item['contentDetails']['duration'];
	if(!empty($response['nextPageToken']))
		getPlaylistItems($playlistId, $maxResults, $response['nextPageToken']);
}
function time_converter($ttime)
{
	$normaltime = strtr($ttime, array("PT" => "", "H" => ":", "M" => ":", "S" => ""));
	if(substr($normaltime, -1) == ':')$normaltime .= '0';
	return $normaltime;
}
?>