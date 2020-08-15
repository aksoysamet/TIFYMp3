<?php 
require_once '../google-api-php-client/vendor/autoload.php';


$DEVELOPER_KEY = 'AIzaSyAvGsE8x4r9KRv0WMKAptDU9j3mTyDw0ZQ';

$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

try {
	// Call the search.list method to retrieve results matching the specified
	// query term.

	# Call the videos.list method to retrieve location details for each video.
	$videosResponse = $youtube->videos->listVideos('id, snippet, contentDetails', array(
	'id' => $_POST['url']
	));
	$videos = '';
	//print_r($videosResponse);
	// Display the list of matching videos.
	foreach ($videosResponse['items'] as $videoResult) {
		$normaltime = str_replace("PT","",$videoResult['contentDetails']['duration']);
		$normaltime = str_replace("H",":",$normaltime);
		$normaltime = str_replace("M",":",$normaltime);
		$normaltime = str_replace("S","",$normaltime);
		if(stotime($normaltime) < 1200)
		{
			$pos = getSorterPos($videoResult['snippet']['title']);
			$videos .= sprintf('%s|%s',
			  trim(mb_substr($videoResult['snippet']['title'], 0, $pos > 40 ? 40 : $pos)),
			  $normaltime
			  );
		}
	}
} catch (Google_Service_Exception $e) {
$videos .= sprintf('A service error occurred: %s',
	htmlspecialchars($e->getMessage()));
} catch (Google_Exception $e) {
$videos .= sprintf('An client error occurred: %s',
	htmlspecialchars($e->getMessage()));
}
echo str_replace(array('Äž','Ãœ','Åž','Ä°','Ã–','Ã‡','ÄŸ','Ã¼','ÅŸ','Ä±','Ã¶','Ã§'),array('Ð','Ü','Þ','Ý','Ö','Ç','ð','ü','þ','ý','ö','ç'),$videos);


function stotime($str)
{
	switch(substr_count($str,':'))
	{
		case 2:
		{
			list($hour, $minute, $second) = sscanf($str, "%d:%d:%d");
			$sure = ($hour*3600)+($minute*60)+($second);
			break;
		}
		case 1:
		{
			list($minute, $second) = sscanf($str, "%d:%d");
			$sure = ($minute*60)+($second);
			break;
		}
		case 0:
		{
			$sure = strval($str);
			break;
		}
	}
	return $sure;
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
?>