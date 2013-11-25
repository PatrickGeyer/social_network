<?php

if(!isset($_COOKIE['id']) || $_COOKIE['id'] == "")
{
	header("location: ../login");
}

else
{
	include_once('config.php');
	include_once('user.class.php');
	include_once('group.class.php');
	include_once('school.class.php');
	include_once('extends.class.php');
	include_once('notifications.class.php');
	include_once('collaborator.class.php');
	include_once('home.class.php');

	$phrases_query = "SELECT * FROM phrases;";
	$phrases_query = $database_connection->prepare($phrases_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$phrases_query->execute();
	$phrases = $phrases_query->fetch(PDO::FETCH_ASSOC);

	$user = new User;
	$group = new Group;
	$school = new School;
	$extend = new Extend;
	$notification = new Notification;
	$collaborator = new Collaborator;
	$home = new Home;
}
?>