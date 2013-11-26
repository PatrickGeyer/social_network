<?php
include_once('database.class.php');

class User extends Database
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
	function getName($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT name FROM users WHERE id = :user_id";
		//print_r($this);
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getYear($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT year FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" =>  $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getSchoolId($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT school_id FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" =>  $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getSchool($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT school FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getProfilePicture($size = "original", $id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		if($size == "original")
		{
			$user_query = "SELECT profile_picture FROM users WHERE id = :user_id";
		}
		else if($size == "thumb")
		{
			$user_query = "SELECT profile_picture_thumb FROM users WHERE id = :user_id";
		}
		else if($size == "icon")
		{
			$user_query = "SELECT profile_picture_icon FROM users WHERE id = :user_id";
		}
		else if($size == "chat")
		{
			$user_query = "SELECT profile_picture_chat_icon FROM users WHERE id = :user_id";
		}
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getGender($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT gender FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getAbout($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT about FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getLanguage($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT default_language FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function getEmail($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT email FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	function isAdmin($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT admin FROM users WHERE id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetchColumn();
		return $user;
	}
	
	function getActivity($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}

		$activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
		school_id = :school_id
		OR (year = :user_year AND school_id = :school_id) 
		OR group_id in (SELECT group_id FROM group_member WHERE member_id = :user_id) 
		OR receiver_id = :user_id) AND user_id = ".$id."
		ORDER BY time DESC";
		$activity_query = $this->database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$activity_query->execute(array(":user_id" => $id, ":school_id" => $this->getSchoolId($id), ":user_year" => $this->getYear($id)));
		$return = $activity_query->fetchAll(PDO::FETCH_ASSOC);
		return $return;

	}
	function updateSettings($about = null, $school = null, $year = null, $email = null, $language = null)
	{
		$sql;
		if(isset($about))
		{
			$sql = 'UPDATE users SET about="'.$about.'"';
		}
		if(isset($school) && $school != "")
		{
			$sql .= ',school = "'.$school.'"';
		}
		if(isset($year) && $school != "")
		{
			$sql .= ', year = "'.$year.'" ';
		}
		if(isset($email) && $school != "")
		{
			$sql .= ',email="'.$email.'" ';
		}
		if(isset($language) && $school != "")
		{
			$sql .= ',default_language="'.$language.'"';
		}
		$sql .= ' WHERE id='.$this->getId();
		$sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sql->execute(array());
	}
	function getBookmarks($id = null)
	{
		if(!isset($id) || $id == "")
		{
			$id = $this->user_id;
		}
		$user_query = "SELECT * FROM bookmark WHERE user_id = :user_id";
		$user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => $id));
		$user = $user_query->fetch();
		return $user;
	}
}
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['about']))
{
	$user = new User;
	if(!isset($_POST['email']))
	{
		$email = $user->getEmail();
	}
	else
	{
		$email = $_POST['email'];
	}
	$user->updateSettings($_POST['about'], $email, $_POST['year']);
}

?>