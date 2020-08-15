<?php 
include 'action_page.php';
include 'db.php';
$db = new DbConnect();
$id = substr($_POST['id'], -11);
if(strlen($id) != 11)
	die("ID Error");
if(IsMp3Exist($id)) {
	@copy('/var/www/html/ibizamusicenc/'.$id.'.mp3','/var/www/html/stream/stream.mp3');
	echo 'http://'.$_SERVER['SERVER_ADDR'].'/stream/stream.mp3';
	flush();
	ob_flush();
	$deyim = $db->getDb()->prepare('UPDATE `musics` SET `playtime` = `playtime` + 1, `lastplay` = NOW() WHERE `id` = ?');
	$deyim->bindParam(1, $id);
	$deyim->execute();
}else
{
	DownloadFile($id);
	if(IsMp4Exist($id)) {
		ConvertMP3($id);
		unlink('/var/www/html/ibizavvideo/'.$id.'.mp4');
		@copy('/var/www/html/ibizamusicenc/'.$id.'.mp3','/var/www/html/stream/stream.mp3');
		echo 'http://'.$_SERVER['SERVER_ADDR'].'/stream/stream.mp3';
		flush();
		ob_flush();
		//LimitControl();
		$deyim = $db->getDb()->prepare('INSERT INTO `musics` (`id`) VALUES (?)');
		$deyim->bindParam(1, $id);
		$deyim->execute();
	}else {
		die("Mp4 Download Error");
	}
}
$db->closeDB();
/*function LimitControl()
{
	global $db;
	if($db == NULL)return;
	if(GetDirectorySize('/var/www/html/ibizamusic') > 62914560)
	{
		$deyim = $db->getDb()->prepare('SELECT `id` FROM `musics` ORDER BY `playtime`, `lastplay` ASC LIMIT 1');
		if($deyim->execute()) {
			$id = $deyim->fetch(PDO::FETCH_ASSOC);
			$id = $id['id'];
			if(IsMp3Exist($id))unlink('/var/www/html/ibizamusic/'.$id.'.mp3');
			$deyim = $db->getDb()->prepare('DELETE FROM `musics` WHERE `id` = ?');
			$deyim->bindParam(1, $id);
			$deyim->execute();
		}
	}
}
function Play($id)
{
	global $db;
	if($db == NULL)return;
	$deyim = $db->getDb()->prepare('SELECT `id` FROM `musics` WHERE `id` = ?');
	$deyim->bindParam(1, $id);
	if($deyim->execute()) {
		if($deyim->rowCount() > 0) {
			$deyim = $db->getDb()->prepare('UPDATE `musics` SET `playtime` = `playtime` + 1, `lastplay` = NOW() WHERE `id` = ?');
			$deyim->bindParam(1, $id);
			$deyim->execute();
		}else{
			$deyim = $db->getDb()->prepare('INSERT INTO `musics` (`id`) VALUES (?)');
			$deyim->bindParam(1, $id);
			$deyim->execute();
		}
	}
}*/
?> 