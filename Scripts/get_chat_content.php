<?php
include_once('config.php');
include_once('user.class.php');

$user = new User;

echo "There have not been any chat entries here yet.";
if($_POST['chat'] == "y" || $_POST['chat'] == "s")
{
	if($_POST['chat'] == "y")
	{
		$db = "SELECT * FROM chat WHERE school = '".$user->getSchool()."' AND sender_year = ".$user->getYear()." AND aimed = 'y';";
		$db_query = $database_connection->prepare($db, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$db_query->execute();
		$chatentry = $db_query->fetchAll(PDO::FETCH_ASSOC);
	}	
	else if($_POST['chat'] == "s")
	{
		$db_query = "SELECT * FROM chat WHERE school='".$user->getSchool()."' AND aimed='s';";
		$db_query = $database_connection->prepare($db_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$db_query->execute();
		$chatentry = $db_query->fetchAll();
	}

	foreach($chatentry as $record)
	{
		$online = $database_connection->prepare("SELECT * FROM users WHERE id = ".$record['sender_id'].";");
		$onlinefetch = $online->fetch(PDO::FETCH_ASSOC);
		$onlinestatus = $onlinefetch['online'];
		if($record['sender_id'] == $user->getId())
		{
			echo "<tr class='mychatname'>";
			echo "<td class='mychatname'><div class='onlinestatus' style='border-left-color: limegreen;'></div>".$record['name'].": </td>";
			echo "</tr>";
			echo "<tr class='mychattext'>";
			echo "<td class='mychattext'>".$record['text']."</td>";
		}
		else
		{
			echo "<tr class='chatname'>";
			if($onlinestatus == true)
			{
				echo "<td class='chatname'><div class='onlinestatus' style='border-left-color: limegreen;'></div>".$record['name'].": </td>";
			}
			else
			{
				echo "<td class='chatname'><div class='onlinestatus' style='border-left-color: orange;'></div>".$record['name'].": </td>";
			}
			echo "</tr>";
			echo "<tr class='chattext'>";
			echo "<td class='chattext'>".$record['text']."</td>";
		}			
		echo "</tr>";
	}
}
else
{
	$db_query = "SELECT * FROM group_chat WHERE group_id=".$_POST['chat'].";";
	$db_query = $database_connection->prepare($db_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$db_query->execute();
	$chatentry = $db_query->fetchAll();
	foreach($chatentry as $record)
	{
		$online=mysql_query("SELECT * FROM users WHERE id = ".$record['member_id'].";");
		$onlinefetch = mysql_fetch_array($online);
		$onlinestatus = $onlinefetch['online'];
		if($record['member_id'] == $user['id'])
		{
			echo "<tr class='mychatname'>";
			echo "<td class='mychatname'><div class='onlinestatus' style='border-left-color: limegreen;'></div>".$record['member_name'].": </td>";
			echo "</tr>";
			echo "<tr class='mychattext'>";
			echo "<td class='mychattext'>".$record['chat_text']."</td>";
		}
		else
		{
			echo "<tr class='chatname'>";
			if($onlinestatus == true)
			{
				echo "<td class='chatname'><div class='onlinestatus' style='border-left-color: limegreen;'></div>".$record['member_name'].": </td>";
			}
			else
			{
				echo "<td class='chatname'><div class='onlinestatus' style='border-left-color: orange;'></div>".$record['member_name'].": </td>";
			}
			echo "</tr>";
			echo "<tr class='chattext'>";
			echo "<td class='chattext'>".$record['chat_text']."</td>";
		}			
		echo "</tr>";
	}
}
?>