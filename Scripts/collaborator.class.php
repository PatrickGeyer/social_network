<?php
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$collaborator = new Collaborator;
	if(isset($_POST['action']) && $_POST['action'] == "getSrc")
	{
		echo $collaborator->getSrc($_POST['id']);
	}
	if(isset($_POST['action']) && $_POST['action'] == "setSrc")
	{
		echo $collaborator->setSrc($_POST['html'], $_POST['id']);
	}
}
class Collaborator
{
	private $link;
	private $dsn;
	private $user;
	private $password;

	public function __construct()
	{
        $this->user    = 'root';
        $this->password    = 'Filmaker1';
        $this->dsn  = 'mysql:dbname=social_network;host=localhost';

        $this->link = new PDO($this->dsn, $this->user, $this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING))
            OR die("There was a problem connecting to the database.");
		return "ha";
       // return true;
	}
	function getSrc($id)
	{
		$sql = "SELECT collaboration_src FROM collaboration WHERE id = ".$id.";";
		$sql = $this->link->prepare($sql);
		$sql->execute();
		$source = $sql->fetchColumn();
		return $source;
	}
	function setSrc($id, $html)
	{
		$sql = "UPDATE collaboration SET collaboration_src = '".$html."' WHERE id = ".$id.";";
		$sql = $this->link->prepare($sql);
		$sql->execute();
	}
	function getName($id)
	{
		$sql = "SELECT collaboration_name FROM collaboration WHERE id = ".$id.";";
		$sql = $this->link->prepare($sql);
		$sql->execute();
		$source = $sql->fetchColumn();
		return $source;
	}
	function setName($id, $name)
	{

	}
}
?>