<?php
include_once('database.class.php');
include_once('user.class.php');

class Chat extends Database
{
	private $user;
	public function __construct()
	{
		parent::__construct();
		$this->user = new User;
	}
	public function submitChat($aimed, $text)
	{
		$text = str_replace("\n", "<br />", $text);
		if($text == "")
		{

		}
		else if($aimed == "s" || $aimed == "y")
		{
			$sql = "INSERT INTO chat(sender_id, `text`, school_id, sender_year, aimed) VALUES(".$this->user->getId().", '".$text."','".$this->user->getSchoolId()."', ".$this->user->getYear().", '".$aimed."');";
		}
		else
		{
			$sql = "INSERT INTO chat(group_id, sender_id, `text`) VALUES(".$aimed.", ".$this->user->getId().", '".$text."');";
		}
		$sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sql->execute();
	}
	function getContent($chat_identifier)
	{
		if($chat_identifier == "y")
		{
			$chat_query = "SELECT * FROM chat WHERE school_id = '".$this->user->getSchoolId()."' AND sender_year = ".$this->user->getYear()." AND aimed = 'y';";
		}	
		else if($chat_identifier == "s")
		{
			$chat_query = "SELECT * FROM chat WHERE school_id = '".$this->user->getSchoolId()."' AND aimed='s';";
		}
		else
		{
			$chat_query = "SELECT * FROM chat WHERE group_id = ".$chat_identifier.";";
		}
		$chat_query = $this->database_connection->prepare($chat_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$chat_query->execute();
		$chat_number = $chat_query->rowCount();
		$chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);

		if($chat_number == 0)
		{
			echo "<tr class='mychatname'><td class='mychatname'>There are no chat entries here yet!</td></tr>";
		}
		foreach($chat_entries as $record)
		{
			$online_query = "SELECT online FROM users WHERE id = :id;";
			$online_query = $this->database_connection->prepare($online_query);
			$online_query->execute(array(":id" => $record['sender_id']));
			$onlinefetch = $online_query->fetchColumn();
			$onlinestatus = $onlinefetch;
			if($record['sender_id'] == $this->user->getId())
			{
				echo "<tr class='mychatname'>";
				echo "<td class='mychatname'><div class='onlinestatus' style='border-left-color: limegreen;'></div>".$this->user->getName($record['sender_id']).": </td>";
				echo "</tr><tr class='mychattext'>";
				echo "<td class='mychattext'>".$record['text']."</td>";
			}
			else
			{
				echo "<tr class='chatname'>";
				if($onlinestatus == true)
				{
					echo "<td class='chatname'><div class='onlinestatus' style='border-left-color: limegreen;'></div>".$this->user->getName($record['sender_id']).": </td>";
				}
				else
				{
					echo "<td class='chatname'><div class='onlinestatus' style='border-left-color: orange;'></div>".$this->user->getName($record['sender_id']).": </td>";
				}
				echo "</tr>";
				echo "<tr class='chattext'>";
				echo "<td class='chattext'>".$record['text']."</td>";
			}			
			echo "</tr>";
		}
	}
}
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$chat = new Chat;
	if(isset($_POST['chat']))
	{
		$chat->getContent($_POST['chat']);
	}
	if(isset($_POST['action']) && $_POST['action'] == "addchat")
	{
		$chat->submitChat($_POST['aimed'], $_POST['chat_text']);
	}
}

?>