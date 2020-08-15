<?php 
include 'YouTubeDownloader.php';
require_once '../google-api-php-client/vendor/autoload.php';
$DEVELOPER_KEY = 'AIzaSyAvGsE8x4r9KRv0WMKAptDU9j3mTyDw0ZQ';
$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);
$youtube = new Google_Service_YouTube($client);
$ytitle = '';
$author = '';
$id = substr($_GET['id'], -11);
try {
	$videosResponse = $youtube->videos->listVideos('id, snippet, contentDetails', array('id' => $id));
	foreach ($videosResponse['items'] as $videoResult) {
		$ytitle = $videoResult['snippet']['title'];
		break;
	}
}catch (Google_Service_Exception $e) {
	printf('A service error occurred: %s',htmlspecialchars($e->getMessage()));
} catch (Google_Exception $e) {
	printf('An client error occurred: %s',htmlspecialchars($e->getMessage()));
}
$json = file_get_contents('https://www.shazam.com/discovery/v2/tr/TR/web/-/search?searchQuery='.urlencode($ytitle).'&pageSize=1&startFrom=0');
$djson = json_decode($json, true);
if(empty($djson['tracks']['hits']))
{
	if(empty($djson['topresult']['track']))
		die('Null oldu');
	else $djson = $djson['topresult']['track'];
}else
	$djson = $djson['tracks']['hits'][0];
//var_dump($djson);
$stitle = $djson['heading']['title'];
$author = $djson['heading']['subtitle'];
?>
<html>
<form action="./action_page.php">
<label>
	<input type="radio" name="img" value="<?php echo 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg'; ?>"> <img src="<?php echo 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg'; ?>">
</label>
<label>
	<input type="radio" name="img" value="<?php echo $djson['images']['default']; ?>" checked> <img src="<?php echo $djson['images']['default']; ?>"><br>
</label>
<label>  
	<input type="radio" name="title" value="<?php echo $ytitle; ?>"> <?php echo $ytitle; ?>
</label>
<label>
	<input type="radio" name="title" value="<?php echo $stitle; ?>" checked> <?php echo $stitle; ?>
</label>
	<input type="text"  name="id" value="<?php echo $id; ?>" hidden>
	<input type="text"  name="author" value="<?php echo $author; ?>" hidden>
<br><br>
	
	<input type="submit" value="Submit" style="height: 100px; width: 100px; left: 250; top: 250;">
</form>
</html>
<?php
/*echo '<h2>'.$title.'</h2><br>';

$ttime_start = microtime_float();
$yt = new YouTubeDownloader();
$targeturl = $yt->getDownloadLinks($id, 'MP4 1080,MP4 720p (HD),MP4 360p');
//var_dump($targeturl);
$targeturl = $targeturl[0]['url'];
//echo $targeturl;

if(!file_exists('/var/www/html/ibizavvideo/'.$id.'.mp4'))
{
	$info = DownloadFile('/var/www/html/ibizavvideo/'.$id.'.mp4', $targeturl);
	echo '<br>';
	echo 'İndirme ' .$info['total_time'] . ' saniye sürdü. Hız '.$info['speed_download'];
	echo '<br>';
	$info = DownloadFile('/var/www/html/ibizavvideo/'.$id.'.jpg', $djson['images']['default']);
	echo '<br>';
	echo 'Resim İndirme ' .$info['total_time'] . ' saniye sürdü. Hız '.$info['speed_download'];
	echo '<br>';
}else {
	echo '<br>';
	echo 'Zaten indirilmiş!';
	echo '<br>';
}
if(!file_exists('/var/www/html/ibizamusic/'.$id.'.mp3'))
{
	$time_start = microtime_float();
	//exec('ffmpeg -i /var/www/html/ibizavvideo/'.$id.'.mkv -vn -acodec libmp3lame -ac 2 -ab 160k -ar 48000 /var/www/html/ibizamusic/'.$id.'.mp3');
	//exec('ffmpeg -i /var/www/html/ibizavvideo/'.$id.'.mp4 -vn -acodec libmp3lame /var/www/html/ibizamusic/'.$id.'.mp3');
	exec('ffmpeg -i /var/www/html/ibizavvideo/'.$id.'.mp4 -vn -acodec libmp3lame -ac 2 -ab 192k -ar 48000 /var/www/html/ibizamusic/temp.mp3');
	exec('ffmpeg -i /var/www/html/ibizamusic/temp.mp3 -i /var/www/html/ibizavvideo/'.$id.'.jpg -map 0:0 -map 1:0 -c copy -id3v2_version 3 -metadata title="'.$title.'" -metadata album_artist="'.$author.'" "/var/www/html/ibizamusic/'.$title.'.mp3"');
	echo '/var/www/html/ibizamusic/'.$title.'.mp3<br>';
	unlink('/var/www/html/ibizamusic/temp.mp3');
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	$time2 = $time_end - $ttime_start;
	echo 'Mp3 çevirme '.$time.' saniye sürdü. Tüm işlemler '.$time2.' saniye sürdü.<br>';
	echo '<img src="'.$djson['images']['default'].'"><br>';
	echo '<h1>'.$title.'</h1><br>';
	echo '<h1>'.$author.'</h1><br>';
}else {
	echo '<br>';
	echo 'Zaten mp3e çevrilmiş!';
	echo '<br>';
}*/

function DownloadFile($savefile, $url)
{
	set_time_limit(0);
	$fp = fopen ($savefile, 'w+');
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	// write curl response to file
	curl_setopt($ch, CURLOPT_FILE, $fp); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	// get curl response
	curl_exec($ch); 
	$info = curl_getinfo($ch);
	//var_dump($info);
	curl_close($ch);
	fclose($fp);
	return $info;
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
?> 