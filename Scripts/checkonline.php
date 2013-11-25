<?php
	include_once('lock.php');

	if(!mysql_query("UPDATE users SET online = 1 WHERE id = ".$user['id'].";"))
	{
		die(mysql_error());
	}

	if(!mysql_query("UPDATE users SET lastactivity = NOW() WHERE id = ".$user['id'].";"))
	{
		die(mysql_error());
	}

	$query = mysql_query("SELECT * FROM users WHERE lastactivity < DATE_SUB(NOW(), INTERVAL 10 SECOND);");
	while($data = mysql_fetch_array($query))
	{ 
		if(!mysql_query("UPDATE users SET online = 0 WHERE id = ".$data['id'].";"))
		{
			die(mysql_error());
		}
	}
?>