<?php

include_once('config.php');

if($_SERVER["REQUEST_METHOD"]  ==  "POST")
{
	$newpassword 	= $_POST['newpassword'];
	$firstname 		= $_POST['firstname']; 
	$lastname 		= $_POST['lastname']; 
	$newname 		= $firstname." ".$lastname;
	$community 		= $_POST['school']; 
	$gender	 		= $_POST['gender']; 
	$email 			= $_POST['email']; 
	$year 			= $_POST['year'];

	$user_query = "SELECT * FROM users WHERE email = :email";
	$user_query = $database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$user_query->execute(array(":email" => $email));
	$count = $user_query->rowCount();
	$user = $user_query->fetch(PDO::FETCH_ASSOC);

	if($count == 1)
	{
		echo '<p style = "background-color:orange;">This email has already been registered by '.$user['name'].' from '.$user['school'].'?</p>';
		//setcookie("id", $user['id'], time()+3600000, '/');
		//setcookie("showchat", 1, time()+3600000, '/');  
	}
	else 
	{

		$school_query = "SELECT * FROM community WHERE name = :school LIMIT 1";
		$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$school_query->execute(array(":school" => $community));
		$community = $school_query->fetch(PDO::FETCH_ASSOC);

		if ($community['name']  ==  "") //if there isn't already a school then make row, add one member and appoint user as leader...
		{
			$school_query = "INSERT INTO community (name, leader) VALUES (:school, :school_name);";
			$school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$school_query->execute(array(":school" => $community, ":school_name" => $newname, ":user_id" => $user['id']));
			$dir = 'School/Files/'.$schoolarray['id'];
			if (!file_exists($dir)) 
			{
				mkdir ($dir, 0777);
			}				
		}
		else //if there's already a school then..
		{

		}
		$user_query = "INSERT INTO users (name, password, community_id, school, year, email,  gender, first_name, last_name) VALUES 
			(:name, :password, :community_id, :school, :year, :email, :gender, :first_name, :last_name);";
		$user_query = $database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":name" => $newname, ":password" => $newpassword, ":community_id" => $community['id'], ":school" => $community['name'], ":year" => $year, ":email" => $email, 
			":gender" => $gender, ":first_name" => $firstname, ":last_name" => $lastname));

		$user_query = "SELECT * FROM users WHERE name = :name and school = :school LIMIT 1";
		$user_query = $database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$user_query->execute(array(":name" => $newname, ":school" => $community['name']));
		$user = $user_query->fetch(PDO::FETCH_ASSOC);

		$update_school = "UPDATE community SET leader_id = :user_id WHERE name = :school_name;";
		$update_school = $database_connection->prepare($update_school, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$update_school->execute(array(":school_name" => $newname, ":user_id" => $user['id']));
		
		$create_files_entry = "INSERT INTO files(user_id, filepath, folder_id) VALUES (:user_id, :filepath, :folder_id);";
		$create_files_entry = $database_connection->prepare($create_files_entry, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$create_files_entry->execute(array(":user_id" => $user['id'], ":filepath" => "Users/Files/".$user['id'], ":folder_id" => 1));

		$dir = '../User/Profilepictures/'.$user['id'];
		mkdir ($dir, 0777);

		$dir = '../User/Files/'.$user['id'];
		mkdir ($dir, 0777);

		setcookie("id", base64_encode($user['id']), time()+3600000, '/');
		setcookie("showchat", 's', time()+3600000,'/');
		header("location: ../settings");
	}
}
?>