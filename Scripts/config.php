<?php
	$dsn = 'mysql:dbname=social_network;host=localhost';
	$db_user = 'social_network';
	$db_password = 'Filmaker1';

	try 
	{
	    $database_connection = new PDO($dsn, $db_user, $db_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	} 
	catch (PDOException $e) {
	    echo 'Connection failed: ' . $e->getMessage();
	}

?>
