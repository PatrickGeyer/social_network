<?php
include_once('Scripts/lock.php');

$school_id = urldecode(base64_decode($_GET['id']));
if($school_id == $user->getSchoolId())
{
	$page_identifier = "school";
}
$profilepicexists = false;

include_once('welcome.php');
include_once('friends_list.php');
include_once('chat.php');

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$savepath = "";
	$savefile = null;

	$description = $_POST['description'];
	$description = mysql_real_escape_string($description);
	for( $count = 0; $count < count($_FILES['file']['name']); $count++) 
	{
		$tmpFilePath = $_FILES['file']['tmp_name'][$count];
		$savepath = "School/Files/".$user['school_id']."/".$_FILES['file']['name'][$count];
		$dir = 'School/Files/'.$user['school_id'];

		if($_FILES['file']['error']['$count'] > 0)
		{
			echo "Error: ".$_FILES['file']['error'];
		}
		else
		{
			if($_FILES['file']['name'][$count] != "")
			{
				if($savefile = move_uploaded_file($tmpFilePath, $savepath))
				{
					if($description != "")
					{
						if(!mysql_query("INSERT INTO schoolfiles (filepath, description, schoolid) VALUES ('".$savepath."', '".$description."', '".$user['school_id']."');"))
						{
							echo "File uploaded to server but mysql server did not update. Users will not be able to view your new profile picture unless by coincidence.";
						}
					}
					else
					{
						if(!mysql_query("INSERT INTO schoolfiles (filepath, description, schoolid) VALUES ('".$savepath."', '".$_FILES['file']['name'][$count]."', '".$user['school_id']."');"))
						{
							echo "File uploaded to server but mysql server did not update. Users will not be able to view your new profile picture unless by coincidence.";

						}
					}
				}
				else
				{
					echo "Failed to save file!";
				}
			}
			else
			{	
			}
		}
	}
}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/style.css">
	<title><?php echo $extend->trimStr($school->getName($school_id), 20);?></title>
</head>
<body>
	<?php 
	if(isset($user->getSchoolId))
	{
		$schooldir = "School/Files/".$school_id;
	}
	?>		
	<center>
	<div class="container">
		<h1><?php echo $school->getName($school_id);?></h1>
		<?php
		if ($school->getProfilePicture('original', $school_id) != "") 
		{
			echo "
			<div class='profilepicturediv' style='background-image:url(".$school->getProfilePicture('original', $school_id).");' 
			onclick='initiateTheater(\"".$school->getProfilePicture('original', $school_id)."\",\"no_text\");'>
			<img style='padding:10px;opacity:0;visibility:hidden;' src='".$school->getProfilePicture('original', $school_id)."'></img></div>";
		}
		?>
			<div class="pseudonym">
				<table class="none">
					<tr>
						<td>
							<label>President:</label>
						</td>
						<td>
							<label>
								<?php 
								echo $school->getLeader($school_id);
								?>
							</label>
						</td>
					</tr>
					<?php 
					// if($school->getAbout($user->getSchoolId()) != "") 
					// {
					// 	echo "<tr><td><label>About:</label></td><td><label><".$school->getAbout($user->getSchoolId())."</label></textarea></td></tr>"; 
					// }
					?>
				</table>
				<br>
				<table>
					<tbody>
					<?php
					$members = $school->getMembers($school_id);
					foreach($members as $member)
					{
						echo "<tr>";
						echo "<td>";
						echo "<a class='user_name' href='user?".$member['id']."'>".$member['name']."</a>";
						echo "</td>";
						echo "<td>";
						echo $member['year'];
						echo "</td>";
						echo "<td>";
						echo $extend->humanTiming(strtotime($member['joined']));
						echo "</td>";
						if($user->isAdmin() == true || $school->getLeaderId($school_id) == $user->getId())
						{
							echo "<td>";
							echo "a";
							echo "</td>";
						}
						echo "</tr>";
					}
					?>
					</tbody>
				</table>
			</div>
	</div>	
</body>
</html>