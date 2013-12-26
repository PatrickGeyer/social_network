<?php
class Database
{
	private $database_user = 'social_network';
	private $database_password = 'Filmaker1';
	private $database_dsn = 'mysql:dbname=social_network;host=localhost';
	public $database_connection;
	public function __construct()
	{
		$this->database_connection = new PDO($this->database_dsn, $this->database_user, $this->database_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING))
			OR die('ERROR');
	}
}
?>