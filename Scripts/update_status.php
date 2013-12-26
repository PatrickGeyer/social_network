<?php
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	include_once('lock.php');
	$status_text = $_POST['status_text'];
	$status_text = str_replace("\n", "<br />", $status_text);
		//$status_text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $status_text);
		//$status_text = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $status_text);
		//$status_text = preg_replace('/(<[^>]+) id=".*?"/i', '$1', $status_text);


	$database_connection->beginTransaction(); 
	try
	{
		$school_query = "INSERT INTO activity (user_id, status_text, type) 
		VALUES(:user_id, :status_text, 'text');";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":user_id" => $user->getId(), ":status_text" => $status_text));
	}
	catch(PDOException $e)
	{
		die("Error:".$e->getMessage());
	}
	$lastInsertId = $database_connection->lastInsertId();

	if($_POST['group_id'] == "s")
	{
		$school_query = "INSERT INTO activity_share (activity_id, community_id, direct) 
		VALUES(".$database_connection->lastInsertId().", :community_id, 1);";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":community_id" => $user->getCommunityId()));
	}
	else if ($_POST['group_id'] == "y")
	{
		$school_query = "INSERT INTO activity_share (activity_id, community_id, year, direct) 
		VALUES(".$lastInsertId.", :community_id, :user_year, 1);";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":user_year" => $user->getPosition(), ":community_id" => $user->getCommunityId()));
	}
	elseif($_POST['group_id'] == 'a')
	{

		$school_query = "INSERT INTO activity_share (activity_id, community_id, direct) 
		VALUES(".$lastInsertId.", :community_id, 0);";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":community_id" => $user->getCommunityId()));

		$school_query = "INSERT INTO activity_share (activity_id, year, direct) 
		VALUES(".$lastInsertId.", :user_year, 0);";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":user_year" => $user->getPosition()));

		foreach ($group->getUserGroups() as $single_group) 
		{
			$school_query = "INSERT INTO activity_share (activity_id, group_id, direct) 
			VALUES(".$lastInsertId.", :group_id, 0);";
			$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$school_query->execute(array(":group_id" => $single_group));
		}
	}
	else
	{			
		$school_query = "INSERT INTO activity_share (activity_id, group_id, direct) 
		VALUES(".$lastInsertId.", :group_id, 1);";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":group_id" => $_POST['group_id']));
	}
	$database_connection->commit(); 

}
?>