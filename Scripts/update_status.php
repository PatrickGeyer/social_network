<?php
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		include_once('lock.php');
		if(isset($_POST['group_id']) && $_POST['group_id'] != "")
		{
			$group_receiver_id = $_POST['group_id'];
		}
		else if ($_POST['share'] == "school")
		{	
			$group_receiver_id = $user->getSchoolId();
		}
		else if ($_POST['share'] == "year")
		{	
			$group_receiver_id = $user->getYear();
		}
		if(isset($_POST['status_text']))
		{
			$status_text = $_POST['status_text'];
			$status_text = str_replace("\n", "<br />", $status_text);
			$status_text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $status_text);
			$status_text = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $status_text);
			$status_text = preg_replace('/(<[^>]+) id=".*?"/i', '$1', $status_text);
			$status_text = str_replace("<img", "<img class='' onclick='initiateTheater();' onload='adjustTheater();'", $status_text);
		}
		if(isset($_POST['status_link']))
		{
			$status_link = $_POST['status_link'];
		}
		if(!isset($status_link))
		{
			$database_connection->beginTransaction(); 
			try
			{
				$school_query = "INSERT INTO activity (user_id, user_gender, ";
				if($_POST['share'] == "school")
				{
					$school_query .= "school_id";
				}
				else if($_POST['share'] == "year")
				{
					$school_query .= "year";
				}
				else if($_POST['share'] == "group")
				{
					$school_query .= "group_id";
				}
				$school_query .= ", status_text, user_name, type) 
				VALUES(:user_id, :user_gender, :id, :status_text, :user_name, 'text');";
				$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$school_query->execute(array(":user_id" => $user->getId(), ":user_gender" => $user->getGender(), 
				":id" => $group_receiver_id, ":status_text" => $status_text, ":user_name" => $user->getName()));
			}
			catch(PDOException $e)
			{
				die("Error:".$e->getMessage());
			}
			if($_POST['share'] == "school")
			{
				$school_query = "INSERT INTO activity_share (activity_id, school_id) 
				VALUES(".$database_connection->lastInsertId().", :school_id);";
				$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$school_query->execute(array(":school_id" => $user->getSchoolId()));
			}
			else if ($_POST['share'] == "year")
			{
				$school_query = "INSERT INTO activity_share (activity_id, year) 
				VALUES(".$database_connection->lastInsertId().", :user_year);";
				$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$school_query->execute(array(":user_year" => $user->getYear()));
			}
			else if ($_POST['share'] == "group")
			{
				$school_query = "INSERT INTO activity_share (activity_id, group_id) 
				VALUES(".$database_connection->lastInsertId().", :group_id);";
				$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$school_query->execute(array(":group_id" => $_POST['group_id']));
			}
			else
			{			
				$school_query = "INSERT INTO activity_share (activity_id, school_id) 
				VALUES(".$database_connection->lastInsertId().", :school_id);";
				$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$school_query->execute(array(":school_id" => $user->getSchoolId()));
			}
			$database_connection->commit(); 
				//die($database_connection->errorInfo());
			
		}
		//header("location: ../home?message=post_success");
	}
?>