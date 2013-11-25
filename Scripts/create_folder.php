<?php
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	include_once("lock.php");
	$current_directory = $_POST['current_directory'];
	if($current_directory == "")
	{
		if(!is_dir($current_directory))
		{
			if(mkdir("../User/Files/".$user['id']."/".$_POST['folder_name'], 0700, true))
			{
				die("success/".$current_directory.$_POST['folder_name']);
			}
			else
			{
				die("error/".$current_directory.$_POST['folder_name']);
			}
		}	
	}
	else
	{
		if(!is_dir($current_directory))
		{
			if(mkdir("../".$current_directory.$_POST['folder_name'], 0700, true))
			{
				die("success/".$current_directory.$_POST['folder_name']);
			}
			else
			{
				die("error/".$current_directory.$_POST['folder_name']);
			}
		}	
	}
}
?>