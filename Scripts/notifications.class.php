<?php
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$notify = new Notification();
	if(isset($_POST['action']) && $_POST['action'] == "mark")
	{
		if($_POST['type'] == "message")
		{
			$notify->markAllMessageSeen();
		}
		else if($_POST['type'] == "notification")
		{	
			$notify->markAllNotificationsSeen();
		}
		else if($_POST['type'] == "network")
		{
			$notify->markAllNetworkSeen();
		}
	}
	if(isset($_POST['action']) && $_POST['action'] == "markNotificationRead")
	{
		$notify->markNotificationRead($_POST['id']);
	}
}
class Notification
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

        return true;
	}
	function getMessage($type = null, $id = null)
	{
		if(!isset($type))
		{
			$user_query = "SELECT * FROM messages WHERE receiver_id = :user_id ORDER BY time DESC;";
		}
		else if($type == 'thread')
		{
			$user_query = "SELECT * FROM messages WHERE receiver_id = :user_id AND thread = ".$id." ORDER BY time DESC;";
		}
		else
		{
			$user_query = "SELECT * FROM messages WHERE receiver_id = :user_id AND id = ".$id." ORDER BY time DESC;";
		}
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
		$user1 = $user_query->fetchAll();
		foreach($user1 as &$message)
		{
			$message_read_query = "SELECT `read`,seen FROM message_read WHERE thread_id = ".$message['thread']." AND receiver_id = ".base64_decode($_COOKIE['id']).";";
			$message_read_query = $this->link->prepare($message_read_query);
			$message_read_query->execute();
			$read = $message_read_query->fetch(PDO::FETCH_ASSOC);
			$message['read'] = $read['read'];
			$message['seen'] = $read['seen'];
		}
		return $user1;
	}
	function getMessageNum()
	{
		$user_query = "SELECT id FROM message_read WHERE receiver_id = :user_id AND `seen` = 0;";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
		$user = $user_query->rowCount();
		return $user;
	}
	function markMessageRead($type = 'thread', $id)
	{
		if($type == "thread")
		{
			$thread = $id;
		}
		$sql = "UPDATE message_read SET seen=1, `read`=1 WHERE thread_id = ".$id." AND receiver_id=".base64_decode($_COOKIE['id']).";";
		$sql = $this->link->prepare($sql);
		$sql->execute();
	}
	function markAllMessageSeen()
	{
		$sql = "UPDATE message_read SET seen=1 WHERE receiver_id=".base64_decode($_COOKIE['id']).";";
		$sql = $this->link->prepare($sql);
		$sql->execute();
	}


	function getNotification()
	{
		$user_query = "SELECT * FROM notification WHERE receiver_id = :user_id ORDER BY time DESC;";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
		$user = $user_query->fetchAll();
		return $user;
	}
	function markAllNotificationsSeen()
	{
			$this->link->query("UPDATE notification SET seen = 1 WHERE receiver_id = ".base64_decode($_COOKIE['id'])."");	
	}
	function getNotificationNum()
	{
		$user_query = "SELECT receiver_id FROM notification WHERE receiver_id = :user_id AND `seen` = 0";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
		$user = $user_query->rowCount();
		$user = ltrim($user, '0');
		return $user;
	}
	function markNotificationRead($id)
	{
		$this->link->query("UPDATE notification SET `read`=1 WHERE id=".$id."");
	}


	function getNetwork()
	{
		$user_query = "SELECT * FROM group_invite WHERE receiver_id = :user_id ORDER BY time DESC;";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
		$user = $user_query->fetchAll();
		return $user;
	}

	function getNetworkNum()
	{
		$user_query = "SELECT id FROM group_invite WHERE receiver_id = :user_id AND `seen` = 0 AND invite_status = 1;";
		$user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
		$user = $user_query->rowCount();
		return $user;
	}
	function markAllNetworkSeen()
	{
		$this->link->query('UPDATE group_invite SET seen=1 WHERE receiver_id = '.base64_decode($_COOKIE['id']).';');
	}
}
?>