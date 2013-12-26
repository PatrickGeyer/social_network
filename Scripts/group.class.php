<?php
include_once('database.class.php');
class Group extends Database
{
	private $user_id;

	public function __construct()
	{
		parent::__construct();
        $this->user_id = base64_decode($_COOKIE['id']);
        return true;
	}
	function getId()
	{
		return base64_decode($_COOKIE['id']);
	}
	function getGroupName($id)
	{
		$user_query = "SELECT group_name FROM `group` WHERE id = :id;";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getAbout($id)
	{
		$user_query = "SELECT group_about FROM `group` WHERE id = :id;";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	public function isMember($user_id, $group_id)
	{
		$sql = "SELECT id FROM group_member WHERE member_id = :user_id AND group_id = :group_id";
		$sql = $this->database_connection->prepare($sql);
		$sql->execute(array(":user_id" => $user_id, ":group_id" => $group_id));
		if($sql->rowCount() == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	function getProfilePicture($size = "chat", $id)
	{
		if($size == "original")
		{
			$user_query = "SELECT group_profile_picture FROM `group` WHERE id = :id";
		}
		else if($size == "thumb")
		{
			$user_query = "SELECT group_profile_picture_thumb FROM `group` WHERE id = :id";
		}
		else if($size == "icon")
		{
			$user_query = "SELECT group_profile_picture_icon FROM `group` WHERE id = :id";
		}
		else if($size == "chat")
		{
			$user_query = "SELECT group_profile_picture_chat FROM `group` WHERE id = :id";
		}
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":id" => $this->user_id));
		$user = $user_query->fetchColumn();
		if(!empty($user) || $user != "")
		{
			return $user;
		}
		else
		{
			return "Images/profile-picture-default-unknown-".$size.".jpg";
		}
	}
	public function getFounderId($group_id)
	{
		$sql = "SELECT group_founder_id FROM `group` WHERE id = :group_id;";
		$sql = $this->database_connection->prepare($sql);
		$sql->execute(array(":group_id" => $group_id));
		return $sql->fetchColumn();
	}
	function getUserGroups($user_id = null)
	{
		if(!isset($user_id) || $user_id == "")
		{
			$user_id = $this->user_id;
		}
		$user_query = "SELECT group_id FROM group_member WHERE member_id = :user_id;";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $user_id));
		$usergroups = $user_query->fetchAll(PDO::FETCH_COLUMN);
		return $usergroups;
	}
}
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	include_once('user.class.php');
	$user = new User;
	if(isset($_POST['action']))
	{
		include_once("lock.php");
		if($_POST['action'] == "leave")
		{
			if($database_connection->query("DELETE FROM group_member WHERE member_id = ".$user->getId()." AND group_id = ".$_POST['group_id'].""))
			{
				die("success/".urlencode(base64_encode($_POST['group_id'])));
			}
		}
		if($_POST['action'] == "deleteG")
		{
			if($database_connection->query("DELETE FROM `group` WHERE id =".$_POST['group_id'].";DELETE FROM `group_member` WHERE group_id =".$_POST['group_id'].";DELETE FROM `group_invite` WHERE group_id =".$_POST['group_id'].";DELETE FROM `group_chat` WHERE group_id =".$_POST['group_id'].";"))
			{
				die("success/");
			}
		}
		if($_POST['action'] == "abdicate")
		{
			if($database_connection->query("INSERT INTO `election` (abdicate_id, abdicate_name, group_id) 
				VALUES (".$user->getId().",'".$user->getName()."', ".$_POST['group_id'].");"))
			{
				if($database_connection->query("INSERT INTO `activity` (user_id, user_gender, group_id, user_name, type)
					VALUES(".$user->getId().", '".$user['gender']."', ".$_POST['group_id'].", '".$user->getName()."', 'abdicate');"))
				{
					die("success/");
				}
				else
				{
					die(mysql_error());
				}
			}
			else
			{
				die(mysql_error());
			}
		}
		if($_POST['action'] == "invite")
		{
			if($database_connection->query("INSERT INTO `group_invite` (inviter_id, inviter_name, receiver_id, group_id) 
				VALUES (".$user->getId().",'".$user->getName()."', ".$_POST['user_id'].", ".$_POST['group_id'].");"))
			{
				die('success/');
			}
			else
			{
				die($database_connection->query());
			}
		}
		if($_POST['action'] == "join")
		{
			$sql = "SELECT id FROM group_member WHERE id = ".$user->getId()." AND group_id = ".$_POST['group_id']."";
			$sql = $database_connection->prepare($sql);
			$sql->execute();
			$number = $sql->rowCount();
			if($number == 0)
			{
				$database_connection->query("INSERT INTO `group_member` (member_id, group_id) VALUES (".$user->getId().", ".$_POST['group_id'].");");
			}
			$sql = "UPDATE `group_invite` SET invite_status = 2,`read`=1,seen=1 WHERE id = ".$_POST['invite_id'].";";
			$sql = $database_connection->prepare($sql);
			$sql->execute();

			//die('success/');
		}
		if($_POST['action'] == "reject")
		{
			if($database_connection->query("UPDATE `group_invite` SET invite_status = 0, `read`=1,seen=1 WHERE id = ".$_POST['invite_id'].";"))
			{
				die('success/');
			}
			else
			{
				die(mysql_error());
			}
		}
	}
}
?>