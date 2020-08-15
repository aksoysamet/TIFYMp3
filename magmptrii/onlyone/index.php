<?php 
include '../converter/db.php';
$files = scandir('/var/www/html/ibizamusicenc');
foreach ($files as $file){ 
	if($file === '.' || $file === '..')continue;
	$deyim = $db->getDb()->prepare('INSERT INTO `musics` (`id`) VALUES (?)');
	$id = explode(".", $files);
	$deyim->bindParam(1, $id[0]);
	$deyim->execute();
} 
?> 