<?php 
include_once('welcome.php');
include_once('chat.php');
include_once('friends_list.php');
$profilepicexists = false;
if(isset($_GET['id']))
{
	$userid = urldecode(base64_decode($_GET['id']));
	if($userid == $user->getId())
	{
		$page_identifier = 'user';
	}
}
?>

<head>
	<link rel="stylesheet" type="text/css" href="CSS/home.css">
	<link rel="stylesheet" type="text/css" href="CSS/user.css">
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<title><?php echo $name = $user->getName($userid);?></title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

	<script>
	function showInvite(group, id, group_id)
	{
		$('#invite_text').append("Invite <em><?php echo $user->getName($userid); ?></em> to join the group <em>" + group + "</em>.</p>");
		$( "#invite_box" ).dialog({
			buttons: 
			[{
				text: "Invite",
				click: function() {
					$.post("Scripts/group_actions.php", {action: "invite", user_id: id, group_id:group_id}, function(response)
					{
						alert(response);
					}
					);
					$( this ).dialog( "close" );
				}
			}],
			beforeClose: 
				function()
				{
					$('#invite_text').html("");
				}
		});
	}
	</script>
	<script>
	$(function()
	{
		$('#about_edit_show').mouseenter(function(){
			$('#profile_about_edit').show();
		}).mouseleave( 
		function(){
			$('#profile_about_edit').hide();
		});

		$('.profilepicture').mouseenter(function(){
			$('#profile_picture_edit').show();
		}).mouseleave( 
		function(){
			$('#profile_picture_edit').hide();
		});
		$('#about_edit_show').blur(function()
		{
			submitData();
		});

		$("#about_edit_show").focusin(function() {
			$("#about_edit_show").css("background","white");
		});
		$("#about_edit_show").focusout(function() {
			$("#about_edit_show").css("background","");
		});
	});
	function submitData()
	{
		var about = $('#about_edit_show').html();
		var email = '';
		var school = '';
		var year = '';
		$.post('Scripts/user.class.php', {about : about, email : email, year : year}, function(response)
		{
			$('#about_saved').fadeIn(function()
				{
					$('#about_saved').fadeOut(1000);
				});

  			//alert(response);
  		});
	}
	</script>
</head>
<body>
	<?php

	$personaldir = "User/Profilepictures/".$userid;
	if(is_dir($personaldir))
	{
		if ($handle = opendir($personaldir)) 
		{
			while (false !== ($entry = readdir($handle))) 
			{
				if(strlen($entry) > 4 )
				{
					$personalfilepath = $personaldir."/".$entry;
					$profilepicexists = true;
				}
			}
		}
	}

	$profilepicture = $user->getProfilePicture('thumb', $userid);
	$gender = $user->getGender($userid);

	$personalfilepath = $profilepicture;

	if($gender == "Male")
	{
		$nofilepath = "Images/profile-picture-default-male.jpg";
	}
	else
	{
		$nofilepath = "Images/profile-picture-default-female.jpg";
	}

	?>
	<div class="container">
		<?php
		if ($profilepicture == "") 
		{
			echo "<div class='profilepicturediv' src='".$nofilepath."'> </div>";
		}
		else
		{
			echo "
			<div class='profilepicturediv' style='background-image:url(".$profilepicture.");' 
			onclick='initiateTheater(\"".$user->getProfilePicture('original', $userid)."\",\"no_text\");'>
			<img style='padding:10px;opacity:0;visibility:hidden;' src='".$profilepicture."'></img></div>";
		}
		?>
		<?php 
		if($userid == $user->getId())
		{
			echo "	<button onclick='window.location.assign(&quot;settings&quot;);' style='position:absolute; right:10px; top:20px;' class='pure-button-success'>Manage</button>";
		}
		?>
		<div class="pseudonym">
			<?php
			echo "<p style='margin:0;font-size:2em;'>".$user->getName($userid)."</p>";
			?>
			<span style="padding-bottom:5px;"><a class='connect' href="school?id=<?php echo urlencode(base64_encode($schooldata['id']));?>">
				<span><?php echo $user->getSchool($userid);?> &bull; Year <?php echo $user->getYear($userid);?></span></a></span>
				<?php
				if($user->getAbout($userid) != "")
				{
					echo "<div style='margin-top:10px;'><span style='color:grey;font-weight:bold;font-size:15px;'>&#8618;</span>
					<span style='margin-left:3px;font-size:12px; margin-top:10px;color:grey;'>About</span>
					<img id='about_saved' style='display:none;' title='Saved' src='Images\Icons\icons/tick-circle.png'></img>
					</div>";
					echo "<div title='Click to Edit'";
					if($userid == $user->getId())
					{
						echo " contenteditable ";
					}
					echo " id='about_edit_show' style='margin-left:20px;padding:3px;width:35%;'>".$user->getAbout($userid);
					echo "</div>";
				}
				if($userid == $user->getId())
				{
				//echo "<span id='profile_about_edit' style='display:none;margin-left:10px;position:relative;'>Edit</span>";
				}
				if($user->getEmail($userid) != "")
				{
				//echo "<p>".$userdata['email']."</p>";
				}?>
			</div>
			<?php 
			if($userid != $user->getId())
			{
				echo "<div hidden style='position:absolute; right:20px; top:20px;' id='invite_text_holder'>
				<button class='pure-button-success' onclick='$(&#39;#group_invites&#39;).slideToggle(&#39;fast&#39;);'>Invite</button>
				</div>";
			}
			?>
			<div style='position:absolute; right:20px; top:80px;' hidden id='group_invites'>
				<div id='group_list'>
					<ul>
						<?php
						foreach($group->getUserGroups() as $users_group)
						{
							$query1 = "SELECT group_id FROM group_member WHERE member_id = :user_id AND group_id = :group_id;";
							$query1 = $database_connection->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
							$query1->execute(array(":user_id" => $userid, "group_id" => $users_group['group_id']));
							$query1 = $query1->fetchColumn();

							if($query1 == "")
							{
								echo "<script name='text_append'>$('#invite_text_holder').show();</script>";
								$query_group1 = "SELECT * FROM `group` WHERE id = :group_id AND allow_member_invite = 1;";
								$query_group = $database_connection->prepare($query_group1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
								$query_group->execute(array(":group_id" => $users_group['group_id']));
								$group_info = $query_group->fetch(PDO::FETCH_ASSOC);

									echo "<li class='friend_list_group' style='border-right:2px solid purple;border-left:2px solid purple; max-height:30px;'
									 onclick='showInvite(&#39;".$group_info['group_name']."&#39;, ".$userid.", ".$group_info['id'].");' style='max-height:20px; text-align:center;padding:5px;background-color: rgba(155,130,255,0.05);'
									>".$group_info['group_name']."</li>";
							}
						}
						?>
					</ul>
				</div>
			</div>
			<div hidden id="invite_box" title="Invite">
				<p id="invite_text">
			</div>	
			<div class='shared_files'>
			</div>
			<div style='margin-top:50px;' id="user_activity">
				<?php 
				$array = $user->getActivity($userid);
				$count = count($array);
				foreach($array as $activity)
				{
					if($count==1)
					{
						echo "<hr class='filter'>";
					}
					else
					{
						echo "<hr>";
					}
					$home->homeify($activity, $database_connection, $user);

				}
				?>
			</div>
		</body>