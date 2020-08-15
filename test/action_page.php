<html>
<?php
/*echo 'ID: '.$_GET['id'].'<br>';
echo 'IMG: '.$_GET['img'].'<br>';
echo 'TITLE: '.$_GET['title'].'<br>';
echo 'AUTHOR: '.$_GET['author'].'<br>';*/
$id = $_GET['id'];
$img = $_GET['img'];
$title = $_GET['title'];
$author = $_GET['author'];

$ttime_start = microtime_float();

if(!file_exists('/var/www/html/ibizavvideo/'.$id.'.mp4'))
{
	DownloadMp4($id);
	$info = DownloadFile('/var/www/html/ibizavvideo/'.$id.'.jpg', $img);
	echo '<br>';
	echo 'Resim İndirme ' .$info['total_time'] . ' saniye sürdü. Hız '.$info['speed_download'];
	echo '<br>';
}else {
	echo '<br>';
	echo 'Zaten indirilmiş!';
	echo '<br>';
}
if(!file_exists('/var/www/html/ibizamusicfu/'.$id.'.mp3'))
{
	$time_start = microtime_float();
	//exec('ffmpeg -i /var/www/html/ibizavvideo/'.$id.'.mkv -vn -acodec libmp3lame -ac 2 -ab 160k -ar 48000 /var/www/html/ibizamusic/'.$id.'.mp3');
	//exec('ffmpeg -i /var/www/html/ibizavvideo/'.$id.'.mp4 -vn -acodec libmp3lame /var/www/html/ibizamusic/'.$id.'.mp3');
	exec('ffmpeg -i /var/www/html/ibizavvideo/'.$id.'.mp4 -vn -acodec libmp3lame -ac 2 -ab 192k -ar 48000 /var/www/html/ibizamusicfu/temp.mp3');
	exec('ffmpeg -i /var/www/html/ibizamusicfu/temp.mp3 -i /var/www/html/ibizavvideo/'.$id.'.jpg -map 0:0 -map 1:0 -c copy -id3v2_version 3 -metadata title="'.$title.'" -metadata album_artist="'.$author.'" "/var/www/html/ibizamusicfu/'.$author.' '.$title.'.mp3"');
	echo '/var/www/html/ibizamusicfu/'.$author.' '.$title.'.mp3<br>';
	unlink('/var/www/html/ibizamusicfu/temp.mp3');
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	$time2 = $time_end - $ttime_start;
	echo 'Mp3 çevirme '.$time.' saniye sürdü. Tüm işlemler '.$time2.' saniye sürdü.<br>';
	echo '<img src="'.$img.'"><br>';
	echo '<h1>'.$title.'</h1>';
	echo '<h1>'.$author.'</h1>';
}else {
	echo '<br>';
	echo 'Zaten mp3e çevrilmiş!';
	echo '<br>';
}

?>
<form action="./index.php">
	<input type="text"  name="id" value="<?php echo $id; ?>">
<br><br>	
	<input type="submit" value="Submit" style="height: 100px; width: 100px; left: 250; top: 250;">
</form>
</html>
<?php
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
function DownloadMp4($id)
{
	exec('youtube-dl \'https://www.youtube.com/watch?v='.$id.'\' -f \'(mp4)[height<=480]\' -o \'/var/www/html/ibizavvideo/%(id)s.%(ext)s\' -q --restrict-filenames --no-cache-dir');
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


?>