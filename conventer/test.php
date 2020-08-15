<?php 
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "TMW1faH2jtrHR2");
define("DB_NAME", "yconverter");
class DbConnect
{
	private $connect;
	public function __construct()
	{
		try {
			$this->connect = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}catch (PDOException $e) {
			print "Hata!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	public function getDb()
	{
		return $this->connect;
	}
	public function closeDB()
	{
		$this->connect = NULL;
	}
}

function IsMp3Exist($id)
{
	return file_exists('/var/www/html/ibizamusic/'.$id.'.mp3');
}

$db = new DbConnect();

$deyim = $db->getDb()->prepare('SELECT `id` FROM `musics`');
if($deyim->execute()) {
	while ($row = $deyim->fetch(PDO::FETCH_ASSOC)) {
		if(!IsMp3Exist($row['id']))
			echo $row['id'].'.mp3 doesnt found<br>';
	}
}

$db->closeDB();
?> 