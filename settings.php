<?php
require_once 'Thumbnail/ThumbLib.inc.php';
include_once('welcome.php');
include_once('friends_list.php');
include_once('chat.php'); 
$allschools = "SELECT * FROM schools";
$allschools = $database_connection->prepare($allschools);
$allschools->execute();
$allschools = $allschools->fetchAll(PDO::FETCH_ASSOC);
$emailvalid = false;

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	include_once('Scripts/lock.php');

	$language = $_POST['language'];
	if($language == "English")
	{
		$language = "en";
	}
	else if($language == "Deutsch")
	{
		$language = "de";
	}

	$school 	= 	$_POST['school'];
	$year 		= 	$_POST['year'];
	$newemail 	= 	$_POST['newemail']; 
	$newabout 	=	$_POST['about']; 

	$updateEmail='UPDATE users SET about="'.$newabout.'", school = "'.$school.'", year = "'.$year.'", email="'.$newemail.'", default_language="'.$language.'" WHERE id="'.$user->getId().'"';
	if (!$database_connection->query($updateEmail))
	{
		die('Error: ' . mysql_error());
	}
	else
	{
		$_COOKIE['email'] = $newemail;
	}
	if($newemail != "")
	{
		if(filter_var($newemail, FILTER_VALIDATE_EMAIL))
		{ 	
			$emailvalid = 0;
		}
		else
		{ 
			$emailvalid = 1;
		} 
	}
	else
	{
		$emailvalid=0;
	}

	$savepath 		= "User/Profilepictures/".$user->getId()."/original".preg_replace("/[^A-Za-z0-9.]/", '_', $_FILES["image"]["name"]);
	$savepath 		= str_replace(" ", "_", $savepath);
	$thumbsavepath 	= "User/Profilepictures/".$user->getId()."/medium".preg_replace("/[^A-Za-z0-9.]/", '_', $_FILES["image"]["name"]);
	$thumbsavepath 	= str_replace(" ", "_", $thumbsavepath);
	$iconsavepath 	= "User/Profilepictures/".$user->getId()."/small".preg_replace("/[^A-Za-z0-9.]/", '_', $_FILES["image"]["name"]);
	$iconsavepath 	= str_replace(" ", "_", $iconsavepath);
	$chatsave 		= "User/Profilepictures/".$user->getId()."/chat".preg_replace("/[^A-Za-z0-9.]/", '_', $_FILES["image"]["name"]);
	$chatsave 		= str_replace(" ", "_", $chatsave);
	$dir 			= 'User/Profilepictures/'.$user->getId(); // make a user profile picture directory

	if($_FILES['image']['error'] > 0)
	{
		switch($_FILES['image']['error'])
		{
			case 1:
			echo "Image too large!";
			break;

			case 2:
			echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
			break;

			case 3:
			echo "The uploaded file was only partially uploaded.";
			break;

			case 4:
				//echo "No file was uploaded.";
			break;

			case 6:
			echo "Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.";
			break;

			case 7:
			echo "Failed to write file to disk. Introduced in PHP 5.1.0.";
			break;

			case 8:
			echo "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; 
			examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.";
			break;

			default:
			echo "Unknown Image Error.";
			break;
		}
	}
	else
	{
		if(!is_dir($dir))
		{
			mkdir($dir, 0777);
		}
		$handle = opendir($dir);
		if($_FILES['image']['name'] != "")
		{
			if($savefile = move_uploaded_file($_FILES["image"]["tmp_name"], $savepath))
			{	
				try
				{
					$thumb = PhpThumbFactory::create($savepath);
				}
				catch(Exception $e)
				{
					// handle error here however you'd like
				}
				$thumb->resize(100);
				$thumb->save($thumbsavepath);
				$thumb->cropFromCenter(40);
				$thumb->save($chatsave);
				$thumb->resize(10);
				$thumb->save($iconsavepath);
				try
				{
					$sql = "UPDATE users SET profile_picture='".$savepath."', profile_picture_thumb = '".$thumbsavepath."', profile_picture_icon='".$iconsavepath."', 
						profile_picture_chat_icon='".$chatsave."' WHERE id =".$user->getId().";";
					$sql = $database_connection->prepare($sql);
					$sql->execute();
				}
				catch(PDOException $e)
				{
					die($e);
				}

				$database_connection->beginTransaction(); 

					if($user->getGender() == "Male")
					{
						$activity_query = "INSERT INTO activity (user_id, user_gender, user_name, school_id, status_text, type) 
						VALUES(:user_id, :user_gender, :user_name, :school_id,' changed his profile picture','profile');
						INSERT INTO activity_share(activity_id, school_id) VALUES(".$database_connection->lastInsertId().", :school_id)";
						$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
						$activity_query->execute(array(":user_id" => $user->getId(), ":user_gender" => $user->getGender(), ":user_name" => $user->getName(),
						":school_id" => $user->getSchoolId()));
					}
					else
					{
						$activity_query = "INSERT INTO activity (user_id, user_gender, user_name, school_id, status_text, type) 
						VALUES(:user_id, :user_gender, :user_name, :school_id,' changed her profile picture','profile')";
						$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
						$activity_query->execute(array(":user_id" => $user->getId(), ":user_gender" => $user['gender'], ":user_name" => $user['name'],
						":school_id" => $user['school_id']));
					}
					$database_connection->commit(); 	
			}
			else
			{
				echo "Failed to save file!";
			}
		}
		if(file_exists($savepath))
		{
			$profilepicexists = true;
		}
		echo "<script>$('#personal_info').load('settings.php #personal_info');</script>";
	}
}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/settings.css">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<title>Settings</title>
</head>
<body>
	<?php
	$personaldir = "User/Profilepictures/".$user->getId();
	if(is_file($personaldir))
	{
		if ($handle = opendir($personaldir)) 
		{
			while (false !== ($entry = readdir($handle))) 
			{
				if(strlen($entry) > 4 )
				{
					$personalfilepath = $personaldir."/".$entry;
				}
			}
		}
	}

	$personalfilepath = $user->getProfilePicture('thumb');
	$gender = $user->getGender();

	if($gender == "Male")
	{
		$nofilepath = "Images/profile-picture-default-male.jpg";
	}
	else
	{
		$nofilepath = "Images/profile-picture-default-female.jpg";
	}
	?>		
	<center>
		<div class="container">
			<h1 class="myheader">Settings</h1>
				<a href="user?id=<?php echo urlencode(base64_encode($user->getId()));?>">
					<p>View as classmate</p>
				</a>
			<div class="profilepicturediv">
				<form action="" method="post" enctype="multipart/form-data">
					<table id="personal_info" class="none" style="width:100%;">								
						<tr>
							<td><img onclick="initiateTheater('<?php echo $user->getProfilePicture('original') ?>', 'no_text');"
							 	 class="profilepicture" src=
								<?php if ($user->getProfilePicture('original') == "") echo'"'.$nofilepath.'"'; else{ echo'"'.$personalfilepath.'"';}?>></img>
							</td>
						</tr>
						<tr>
							<td><input type="file" name="image"></td>
						
							<td><input class="small" type="submit" name="submit" value="Upload"></td>
						</tr>
					</table>
					<hr>
					<table class="none" style="border-spacing: 10px; width:100%;">
						<tr>
							<td><textarea class="thin" placeholder="About..." style="width:100%; height: 100px;" id="about" name="about" autocomplete="off" type="text"><?php echo $user->getAbout();?></textarea></td>
						</tr>
						<tr>
							<td><input type="text" placeholder="Email..."autocomplete="off" name="newemail" value="<?php 
								if($user->getEmail() != '')
								{
									echo $user->getEmail();
								}
								?>"
								 /></td><td>
							<?php if($emailvalid != 0)echo "This email is invalid.";?></td>
						</tr>
						<tr>
							<td><div class="styled-select"><select style="width:100%;" name="language">
								<?php 
								if($user->getLanguage() == "en")
								{
									echo "<option selected>English</option><option>Deutsch</option>";
								} 
								else 
								{
									echo "<option>English</option><option selected>Deutsch</option>";
								}
								?>
							</select></div></td>
						</tr>
						<tr>
							<td><div class="styled-select"><select id="schoolselect" style="width:100%;" name="school">'
								<?php
								foreach($allschools as $schools) 
								{ 
									if($schools['name'] == $user->getSchool())
									{
										echo "<option selected>".$schools['name']."</option>";
									}
									else
									{
										echo "<option>".$schools['name']."</option>";
									}
								}
								?>
								<option>*Register a new School</option>
							</td> 
						</tr>
						<tr>
							<td><label>Select Year:</label><div class="styled-select"><select style="width:100%;" name="year">
								<?php 
								$years = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14);
								foreach ($years as $year) 
								{
									if($year == $user->getYear())
									{
										echo "<option selected>".$year."</option>";
									}
									else
									{
										echo "<option>".$year."</option>";
									}
								}
								?>
							</td>
						</tr>
					</table>
					<hr>
					<input type="submit" value="Save"><br/>	
				</div>
			</form>		
		</div>		
	</center>
</body>
</html>