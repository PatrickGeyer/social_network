<?php
if(!isset($_COOKIE['current_directory']) && $_POST['dir'] != "../")
{
	$current_dir = $_POST['dir']."/";
	setcookie("current_directory", $current_dir, time() + 3600000);
	$_COOKIE["current_directory"] = $current_dir;
}
if(isset($_POST['dir']) && $_POST['dir'] != "../")
{
	$current_dir = $_POST['dir']."/";
	setcookie("current_directory", $current_dir, time() + 3600000);
	$_COOKIE["current_directory"] = $current_dir;
}
else
{
	//echo $_COOKIE['current_directory'];
}
if($_POST['dir'] == "../")
{
	$current_dir = $_COOKIE['current_directory'];
	$current_dir = dirname($current_dir);
	setcookie("current_directory", $current_dir, time() + 3600000);
	$_COOKIE["current_directory"] = $current_dir;
}
echo $_COOKIE['current_directory'];
?>