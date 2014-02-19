<?php
	include_once('lock.php');
//	$sql = "UPDATE user SET online = 0 WHERE id = ".$user->getId().";";
//	$sql = $database_connection->prepare($sql);
//	$sql->execute();
	setcookie("id", "", -1, "/");
	header("location: ../login");
?>