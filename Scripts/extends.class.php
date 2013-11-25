<?php
include_once('database.class.php');
include_once('home.class.php');

class Extend extends Database
{
	private $user_id;
	private $phrases;
	public function __construct()
	{
		parent::__construct();

		$this->user_id = base64_decode($_COOKIE['id']);
		$phrases_query = "SELECT * FROM phrases;";
		$phrases_query = $this->database_connection->prepare($phrases_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$phrases_query->execute();
		$phrases = $phrases_query->fetch(PDO::FETCH_ASSOC);
		return true;
	}
	function trimStr($string, $length)
	{
		$string = (strlen($string) > $length) ? substr($string,0,$length).'...' : $string;
		return $string;
	}
	function humanTiming ($time)
	{
		$time = time() - $time; 
		// to get the time since that moment

		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
			);

		foreach ($tokens as $unit => $text) 
		{
			if ($time < $unit)
				continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'')." ago";
		}
	}
	function getImageSize($src)
	{
		if($src[0] != ".")
		{
			$src = "../".$src;
		}
		list($width, $height, $type, $attr) = getimagesize($src);
		$array[0] = $width;
		$array[1] = $height;
		echo json_encode($array);
	}

	function sendMessage($reply, $message, $title = null, $receivers)
	{
		$this->database_connection->beginTransaction(); 

		$getthreadvalue = "SELECT MAX(thread) FROM messages;";

		$getthreadvalue = $this->database_connection->prepare($getthreadvalue, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$getthreadvalue->execute(array());
		$message = ($message);
		$title = ($title);
		$thread = $getthreadvalue->fetchColumn();
		$threadnew = $thread[0] + 1;
		$threadreply = $threadnew - 1;

		if($reply == 0)
		{
			foreach($receivers as $receiver)
			{
				$message_query = "INSERT INTO  message_read(receiver_id, sender_id, thread_id) VALUES (:receiver_id, :sender_id, :thread_id)";
				$message_query = $this->database_connection->prepare($message_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$message_query->execute(array(":receiver_id" => $receiver, ":sender_id" => $this->user_id, ":thread_id" => $threadnew));

				$message_query = "INSERT INTO messages (sender, sender_id, receiver_id, title, message, thread) VALUES (:user_name, :user_id, :receiver_id, :title, :message, :thread_id);";
				$message_query = $this->database_connection->prepare($message_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$message_query->execute(array(":user_name" => "Remove this column.", ":user_id" => $this->user_id, ":receiver_id" => $receiver, ":title" => $title, ":message" => $message, ":thread_id" => $threadnew));
			}
			echo "success/".$threadnew;
		}
		else
		{
			foreach($receivers as $receiver)
			{
				$message_query = "INSERT INTO  message_read(receiver_id, sender_id, thread_id) VALUES (:receiver_id, :sender_id, :thread_id)";
				$message_query = $this->database_connection->prepare($message_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$message_query->execute(array(":receiver_id" => $receiver, ":sender_id" => $this->user_id, ":thread_id" => $threadreply));

				$message_query = "INSERT INTO messages (sender, sender_id, receiver_id, title, message, thread) VALUES (:user_name, :user_id, :receiver_id, :title, :message, :thread_id);";
				$message_query = $this->database_connection->prepare($message_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$message_query->execute(array(":user_name" => "Remove this column.", ":user_id" => $this->user_id, ":receiver_id" => $receiver, ":title" => $title, ":message" => $message, ":thread_id" => $threadreply));
			}
			echo "success/".$threadreply;
		}
		$this->database_connection->commit();
	}
	function submitComment($comment, $post_id)
	{
		if($comment != '')
		{
			$sql = "INSERT INTO comments (commenter_id, commenter_name, post_id, comment_text) VALUES (".$this->user_id.", 'remove this column', ".$post_id.", '".$comment."');";
			$sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$sql->execute();
		}
	}
	function like($activity_id, $receiver_id)
	{
		$who_liked_query = "SELECT * FROM `votes` WHERE post_id = :activity_id AND user_id = :user_id;";
		$who_liked_query = $this->database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$who_liked_query->execute(array(":activity_id" => $activity_id, ":user_id" => $this->user_id));
		$like_count = $who_liked_query->rowCount();

		if($like_count < 1)
		{
			$insert_query = "INSERT INTO `votes` (post_id, user_id, vote_value) VALUES( :activity_id, :user_id, 1);";
			$insert_query = $this->database_connection->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$insert_query->execute(array(":activity_id" => $activity_id, ":user_id" => $this->user_id));
		}
		else
		{
			$who_liked = $who_liked_query->fetch(PDO::FETCH_ASSOC);
			if($who_liked['vote_value'] == 2)
			{
				$sql = "UPDATE 	`votes` SET vote_value = 1 WHERE post_id = :activity_id AND user_id = :user_id;";
				$sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sql->execute(array(":activity_id" => $activity_id, ":user_id" => $this->user_id));
			}
			else
			{
				$sql = "UPDATE 	`votes` SET vote_value = 2 WHERE post_id = :activity_id AND user_id = :user_id;";
				$sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$sql->execute(array(":activity_id" => $activity_id, ":user_id" => $this->user_id));
			}
		}

		$who_liked_query = "INSERT INTO notification (post_id, receiver_id, sender_id, type) VALUES(:activity_id, :receiver_id, :sender_id, :type);";
		$who_liked_query = $this->database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$who_liked_query->execute(array(":activity_id" => $activity_id, ":receiver_id" => $receiver_id, ":sender_id" => $this->user_id, ":type" => "like"));

		$who_liked_query = "SELECT * FROM `votes` WHERE vote_value = 1 AND post_id = :activity_id;";
		$who_liked_query = $this->database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$who_liked_query->execute(array(":activity_id" => $activity_id));
		$like_count = $who_liked_query->rowCount();
		echo $like_count;
	}
	function in_array_r($needle, $haystack, $strict = false) 
	{
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
	            return true;
	        }
	    }
   	 	return false;
	}
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$extend = new Extend;
	//if(isset($_POST['activity_id']))
	//{
	//	$home->getComments($_POST['activity_id']);
	//}
	if(isset($_POST['action_image']))
	{
		$extend->getImageSize($_POST['src']);
	}
	if(isset($_POST['action']) && $_POST['action'] == 'sendMessage')
	{
		$extend->sendMessage($_POST['reply'], $_POST['message'], $_POST['title'], $_POST['receivers']);
	}
	if(isset($_POST['action']) && $_POST['action'] == 'submitComment')
	{
		$extend->submitComment($_POST['comment_text'], $_POST['post_id']);
	}
	if(isset($_POST['action']) && $_POST['action'] == 'like')
	{
		$extend->like($_POST['id'], $_POST['receiver_id']);
	}
}