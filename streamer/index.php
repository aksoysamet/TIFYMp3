<?php 
$id = $_POST['url'];
$url = 'http://panel.turkibiza.net/palmymp3/conventer/?id='.$id;
$result = @file_get_contents($url, false) or
	die ("Host Error");
$result = trim($result);
if (strpos($result, 'http') !== false) {
	DownloadFile('/var/www/html/ibizamusic/stream.mp3',$result);
	echo 'http://'.$_SERVER['SERVER_NAME'].'/ibizamusic/stream.mp3';
}else
	die("File Error");

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
/*chown -R apache:apache /var/www/html/directory_to_write
chcon -R -t httpd_sys_content_t /var/www/html/directory_to_write
chcon -R -t httpd_sys_rw_content_t /var/www/html/directory_to_write*/
?> 