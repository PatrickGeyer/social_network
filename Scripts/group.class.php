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
	function getProfilePicture($size = "original", $id)
	{
		if($size == "original")
		{
			$user_query = "SELECT profile_picture FROM `group` WHERE id = :id";
		}
		else if($size == "thumb")
		{
			$user_query = "SELECT profile_picture_thumb FROM `group` WHERE id = :id";
		}
		else if($size == "icon")
		{
			$user_query = "SELECT profile_picture_icon FROM `group` WHERE id = :id";
		}
		else if($size == "chat")
		{
			$user_query = "SELECT profile_picture_chat_icon FROM `group` WHERE id = :id";
		}
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":id" => base64_decode($_COOKIE['id'])));
		$user = $user_query->fetchColumn();
		return $user;
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
		$usergroups = $user_query->fetchAll(PDO::FETCH_NUM);
		return $usergroups;
	}
}
?>