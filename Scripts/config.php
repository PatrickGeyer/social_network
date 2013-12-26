<?php
	$dsn = 'mysql:dbname=social_network;host=localhost';
	$user = 'social_network';
	$password = 'Filmaker1';

	try 
	{
	    $database_connection = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	} 
	catch (PDOException $e) {
	    echo 'Connection failed: ' . $e->getMessage();
	}

?>
