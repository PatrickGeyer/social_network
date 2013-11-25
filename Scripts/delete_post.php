<?php
	include_once('config.php');
	$post_id = $_POST['post_id'];
	
	$school_query = "DELETE FROM activity WHERE id = :post_id; DELETE FROM activity_share WHERE activity_id = :post_id";
	$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$school_query->execute(array(":post_id" => $post_id));
	//if(!mysql_query("DELETE FROM activity WHERE id = ".$post_id.";"))
	//{
	//	die(mysql_error());
	//}
?>