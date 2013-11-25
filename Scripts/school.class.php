<?php
class School
{
	private $link;
	private $dsn;
	private $user = 'root';
	private $password;

	public function __construct()
	{
        $this->username    = 'root';
        $this->password    = 'Filmaker1';
        $this->dsn  = 'mysql:dbname=social_network;host=localhost';

        $this->link = new PDO($this->dsn, $this->user, $this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING))
            OR die("There was a problem connecting to the database.");

        return true;
	}
	function getId()
	{
		return base64_decode($_COOKIE['id']);
	}
	public function getName($id = null)
	{
		$user_query = "SELECT name FROM schools WHERE id = :user_id";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	public function getLeader($id)
	{
		$user_query = "SELECT leader FROM schools WHERE id = :user_id";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	public function getLeaderId($id)
	{
		$user_query = "SELECT leader_id FROM schools WHERE id = :user_id";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	public function getMembers($id)
	{
		$user_query = "SELECT * FROM users WHERE school_id = :user_id";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchAll();
		return $user;
	}
	public function getAbout($id)
	{
		$user_query = "SELECT about FROM schools WHERE id = :user_id";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	public function getProfilePicture($size="original", $id)
	{
		$user_query = "SELECT profile_picture FROM schools WHERE id = :user_id";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
}
?>