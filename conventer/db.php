<?php
include_once 'config.php';
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
?>