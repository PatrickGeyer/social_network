<?php
require_once 'Thumbnail/ThumbLib.inc.php';
include_once('welcome.php');
include_once('chat.php');
include_once('friends_list.php');
$group_id = urldecode(base64_decode($_GET['id']));
$group_query = "SELECT * FROM `group` WHERE id = ".$group_id." LIMIT 1";
$group_query = $database_connection->prepare($group_query);
$group_query->execute();
$group = $group_query->fetch();

$leader_query = "SELECT * FROM users WHERE id = ".$group['group_founder_id']."";
$leader_query = $database_connection->prepare($leader_query);
$leader_query->execute();
$leader = $leader_query->fetch();

$member_query = "SELECT * FROM group_member WHERE group_id = ".$group['id']." AND member_id =".$user->getId()." LIMIT 1";
$member_query = $database_connection->prepare($member_query);
$member_query->execute();
$is_member = $member_query->rowCount();

$activity_query = "SELECT * FROM activity WHERE 
			id IN (SELECT activity_id FROM activity_share WHERE group_id = :group_id)
			ORDER BY time DESC";
			$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$activity_query->execute(array(":group_id" => $group_id));
?>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/home.css">
	<link rel="stylesheet" type="text/css" href="CSS/user.css">
	<title>Group - <?php echo $group['group_name'];?></title>
	<script>
	var receivers = [];
	function addreceiver(new_receiver, new_receiver_name)
	{
		var found = $.inArray(new_receiver, receivers);
		if(found != -1)
		{

		}
		else
		{
			receivers.push(new_receiver);
			$('#names_input').val("");
			var html = "<td style='min-width:100px;' id = '"+new_receiver+"'> \
			<div class='added_name'><span style='font-family:century gothic;'>"+new_receiver_name+"</span> \
			<span class='delete_receiver' onclick='removereceiver("+new_receiver+");'>x \
			</span></div></td>";
			$('#names_slot').before(html);
		}
	}
	function removereceiver(receiver_id)
	{
		var index = receivers.indexOf(receiver_id);
		if (index > -1) 
		{
			receivers.splice(index, 1);
		}
		$('#' + receiver_id).remove();
	}
	function sendMessage()
	{
		var reply = false;
		var title = $('#title').val();
		var message = $('#1message').val();
		$.post("Scripts/sendmessage.php", { title: title, message: message, receivers: receivers, reply: reply}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				window.location.replace("message?thread=" + status[1]);
			}
		});
	}
	function leaveGroup(group_id)
	{
		$.post("Scripts/group_actions.php", { action: "leave", group_id : group_id}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				window.location.replace("group?id=" + status[1]);
			}
		});(group_id);
	}
	function deleteGroup(group_id)
	{
		$.post("Scripts/group_actions.php", { action: "delete", group_id : group_id}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				window.location.replace("home");
			}
		});(group_id);
	}
	function abdicateGroup(group_id)
	{
		$.post("Scripts/group_actions.php", { action: "abdicate", group_id : group_id}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				window.location.replace("home");
			}
		});(group_id);
	}
	function toggleActions()
	{
		$('.action').slideToggle();
	}
	</script>
</head>
<body>
	<?php
	$personaldir = "User/Profilepictures/".$user->getId();
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
	$nofilepath = "Images/profile-picture-default-male.jpg";
	?>		
		<div class="container">
			<span class='thin_blue_header'><?php echo $group['group_name']; ?></span><br />
			<div style='float:right;'>
				<?php
				if($leader['id'] == $user->getId())
				{
					echo '<button class="pure-button-error action" onclick="deleteGroup('.$group['id'].');">Delete Group</button>';
					echo '<button class="pure-button-yellow action" onclick="abdicateGroup('.$group['id'].');">Abdicate</button>';
					echo '<button class="pure-button-success action" onclick="editGroup('.$group['id'].');">Edit</button>';
				}

				if($is_member > 0)
				{
					echo '<button class="pure-button-primary action" onclick="leaveGroup('.$group['id'].');">Leave Group</button>';
				}
				?>
			</div>
				<form action="" method="post" enctype="multipart/form-data">
					<div style='top:50px;' class="pseudonym">
						<?php
						?>
						<span class='thin_subtitle'>Admin: <?php echo $leader['name'];?></span><br />
						<?php if($group['group_about'] != ""){echo "<td>About: ".$group['group_about']."</span><br />";} ?>
					</div>
				</form>
				<div style='margin-top:250px;margin-left:0px;' id="user_activity">		
					<?php 
				$array = $activity_query->fetchAll(PDO::FETCH_ASSOC);
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
					$extend->homeify($activity, $database_connection, $user);

				}
				?>
				</div>
			</div>
		</div>
	</div>
</body>