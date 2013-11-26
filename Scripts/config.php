<?php
	$dsn = '**:dbname=***;host=***';
	$user = '****';
	$password = '****';

	try 
	{
	    $database_connection = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	} 
	catch (PDOException $e) {
	    echo 'Connection failed: ' . $e->getMessage();
	}

?>
