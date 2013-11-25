<?php
	include_once('lock.php');
	$file_name = $_POST['file_name'];
	$file_path = $_POST['file_path'];
	$description = $_POST['comment'];
	$receivers = $_POST['receivers'];
	
	$database_connection->beginTransaction();
	if(!$database_connection->query("INSERT INTO activity(user_id, user_name, type, status_text, description, user_gender)
		VALUES(".$user->getId().", '".$user->getName()."', 'file', '".$file_path."', '".$description."', '".$user->getGender()."');"))
	{
		echo "error/".mysql_error();
	}
	$activity_id = $database_connection->lastInsertId();

	foreach($receivers as $receiver)
	{
		$split = explode('/', $receiver);
		if($split[0] == "user")
		{
			if(!$database_connection->query("INSERT INTO activity_share(activity_id, receiver_id)
			 VALUES(".$activity_id.", ".$split[1].");"))
			{
				echo "error_share/".mysql_error();
			}
		}
		if($split[0] == "group")
		{
			if(!$database_connection->query("INSERT INTO activity_share(activity_id, group_id)
			 VALUES(".$activity_id.", ".$split[1].");"))
			{
				echo "error_share/".mysql_error();
			}
		}
		if($split[0] == "school")
		{
			if(!$database_connection->query("INSERT INTO activity_share(activity_id, school_id)
			 VALUES(".$activity_id.", ".$split[1].");"))
			{
				echo "error_share/".mysql_error();
			}
		}
		if($split[0] == "year")
		{
			if(!$database_connection->query("INSERT INTO activity_share(activity_id, year)
			 VALUES(".$activity_id.", ".$split[1].");"))
			{
				echo "error_share/".mysql_error();
			}
		}
	}
	$database_connection->commit();
?>