<?php
include_once('Scripts/lock.php');
if(!isset($page_identifier))
{
	$page_identifier = "none_set";
}
?>
<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script>
	function getnames(value)
	{
		$.post("Scripts/searchbar.php", { search:"universal", input_text: value}, function(data){
			$('#names_universal').empty();
			$('#names_universal').append(data);
		});
	}
	</script>
	<script>
	$(document).click(function(e)
	{
		$("#names_universal").hide();	
	});	// ***ADD*** if element is input do nothing!
	</script>
	<script>
	var invited_members = [];
	function addreceiver(new_receiver, new_receiver_name)
	{
		var found = $.inArray(new_receiver, invited_members);
		if(found != -1)
		{

		}
		else
		{
			invited_members.push(new_receiver);
			$('#names_input').val("");
			var html = "<td style='min-width:100px;' id = '"+new_receiver+"'> \
			<div class='tag-triangle'></div><div class='added_name'><span style='font-family:century gothic;'>"+new_receiver_name+"</span> \
			<span class='delete_receiver' onclick='removereceiver("+new_receiver+");'>x \
			</span></div></td>";
			$('#names_slot').before(html);
		}
	}
	function removereceiver(receiver_id)
	{
		var index = invited_members.indexOf(receiver_id);
		if (index > -1) 
		{
			invited_members.splice(index, 1);
		}
		$('#' + receiver_id).remove();
	}
	function createGroup()
	{
		var group_name = $('#group_name').val();
		var group_about = $('#group_about').val();
		var group_type = $('#group_type').val();
		$.post("creategroup.php", { group_name: group_name, group_about: group_about, group_type: group_type, invited_members: invited_members}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				window.location.replace("group?id=" + status[1]);
			}
		});
	}
	</script>

	<script>
	setInterval("update()", 2000);
	function update() 
	{ 
		$.post("Scripts/checkonline.php");
	} 
	</script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="Scripts/main.js"></script>
	<link rel="stylesheet" type="text/css" href="CSS/style.css" />
	<link rel="stylesheet" type="text/css" href="CSS/welcome.css" />
	<link rel="icon" type="image/png" href="favicon.png"/>
	<script>
	$(function()
	{
		$(document).on('click', "#current_page", function(event)
		{ 
			event.stopPropagation();
			$(".general").slideDown("fast"); 
			$(".personal").slideUp("fast"); 
			$("#messagediv").slideUp("fast");
			$("#notificationdiv").slideUp("fast");
			$("#networkdiv").slideUp("fast");
			$("#geardiv").slideUp("fast");
		});
		$(document).on('click', "#home_icon", function(event)
		{ 
			event.stopPropagation();
			window.location.replace("home");
			$("#notificationdiv").slideUp("fast");
			$("#networkdiv").slideUp("fast");
			$("#geardiv").slideUp("fast");
		});
		$(document).on('click', "#personal",function(event)
		{
			event.stopPropagation();
			$(".personal").slideDown("fast"); 
			$(".general").slideUp("fast"); 
			$("#messagediv").slideUp("fast");
			$("#notificationdiv").slideUp("fast");
			$("#networkdiv").slideUp("fast");
			$("#geardiv").slideUp("fast");
		});
		$(document).on('click', "#message_click", function(event)
		{
			markAllSeen('message');
			event.stopPropagation();
			$("#messagediv").slideDown("fast"); 
			$(".personal").slideUp("fast"); 
			$(".general").slideUp("fast"); 
			$("#notificationdiv").slideUp("fast");
			$("#networkdiv").slideUp("fast");
			$("#geardiv").slideUp("fast");
		});
		$(document).on('click', "#notification_click", function(event)
		{
			markAllSeen('notification');
			event.stopPropagation();
			$("#notificationdiv").slideDown("fast"); 
			$(".personal").slideUp("fast"); 
			$(".general").slideUp("fast"); 
			$("#messagediv").slideUp("fast");
			$("#networkdiv").slideUp("fast");
			$("#geardiv").slideUp("fast");
		});
		$(document).on('click', "#network_click", function(event)
		{
			markAllSeen('network');
			event.stopPropagation();
			$("#notificationdiv").slideUp("fast"); 
			$(".personal").slideUp("fast"); 
			$(".general").slideUp("fast"); 
			$("#messagediv").slideUp("fast");
			$("#networkdiv").slideDown("fast");
			$("#geardiv").slideUp("fast");
		});
		$(document).on('click', "#gear_click", function(event)
		{
			event.stopPropagation();
			$("#notificationdiv").slideUp("fast"); 
			$(".personal").slideUp("fast"); 
			$(".general").slideUp("fast"); 
			$("#messagediv").slideUp("fast");
			$("#geardiv").slideDown("fast");
		});
		$(document).on('click', "html", function()
		{ 		
			$(".general").slideUp("fast"); 
			$(".personal").slideUp("fast"); 
			$("#messagediv").slideUp("fast");
			$("#notificationdiv").slideUp("fast");
			$("#networkdiv").slideUp("fast");
			$("#geardiv").slideUp("fast");
		});
	});
function markAllSeen(type)
{
	$.post("Scripts/notifications.class.php", {action : "mark", type : type }, function(response)
	{
		//alert(response);
	});
}
function markNotificationRead(id, nextPage)
{
	$.post("Scripts/notifications.class.php", {action : "markNotificationRead", id : id }, function(response)
	{
		window.location.assign(nextPage);
	});
}
</script>

<script>
</script>
<script>
$(function()
{
	if(document.title != "")
	{
		$('#current_page_link').prepend(document.title);
	} 
	else
	{
		$('#current_page_link').prepend("Page");
	}
});
</script>
</head>
<body id="else" class="welcome">
	<div class="headerbar">
		<div id="refresh">
			<img id='logo' style='position:absolute;left:40px;top:4px;max-height:25px;opacity:0.2;cursor:pointer;' onclick='window.location.assign("home");' src='Images/reallogo.png'></img>
			<div class="navigation">
				<a href='user?id=<?php echo urlencode(base64_encode($user->getId())); ?>'>
					<div class="user_info <?php if($page_identifier == "user"){echo "current_page_user";} ?>" style='cursor:pointer; padding-left:5px; margin-left:-5px;margin-bottom:10px;'>
						<table style='border-collapse: collapse;'>
							<tr>
								<td rowspan='2' style='vertical-align:bottom;'>
									<img src = '<?php echo $user->getProfilePicture("chat"); ?>'></img>
								</td>
								<td>
									<span class="current_user_name_edit"><?php echo $extend->trimStr($user->getName(), 20); ?></span>
								</td>
							</tr>
							<tr>
								<td>
									<span class='edit_user_text'><?php echo $extend->trimStr($user->getSchool(), 20); ?></span>
								</td>
							</tr>
						</table>
					</div>
				</a>
				<hr style='z-index:-1;width:200px;margin-left:-2px;'>
				<ul class="navigation_list"> 
					<li class="nav_option <?php if($page_identifier == "home") { echo "current_page";} ?>"><a class="nav_option" href="home">Home</a></li> 
					<li class="nav_option <?php if($page_identifier == "school") { echo "current_page";} ?>"><a class="nav_option" href="school?id=<?php echo urlencode(base64_encode($user->getSchoolId()));?>">School</a></li> 
					<li class="nav_option <?php if($page_identifier == "files") { echo "current_page";} ?>"><a class="nav_option" href="files">My Files</a></li> 
					<li class="nav_option <?php if($page_identifier == "inbox") { echo "current_page";} ?>"><a class="nav_option" href="message">Inbox</a></li>
				</ul>
			</div>
			<div class="container_headerbar">
				<!-- <div id="welcome_box" class="welcome">
					<ul border="0">
						<li class='name'> 
							<a href="user?id=<?php //echo urlencode(base64_encode($user->getId())); ?>" style="height:20px;">
								<?php
								//echo "<img class='profile_picture_small_welcome' src='".$user->getProfilePicture('icon')."'></img>";
								//echo "<span style='margin-right:15px;white-space:nowrap;'>".$extend->trimStr($user->getName(),20)."</span>";
								?>
							</a>
						</li>
					</ul>
				</div> -->
				<div style="position:absolute;right:500px;top:0;">
					<div class="message" id="message_click">
						<img id="message" class ="message" src='Images/Icons/Icon Pacs/glyph-icons/glyph-icons/PNG/Mail.png'></img>
						<div id="messagediv" class="popup_div">
							<div class="popup_top">
								<span>Messages</span>
								<a href="message">
									<img class ="composemessage" src='Images/Icons/icons/mail--pencil.png'></img>
								</a>
							</div>
							<div class="popup_content scroll_medium">
								<ul class="message"> 
									<?php 
									$message_count = $notification->getMessageNum();
									if ($message_count == 0)
									{
										echo "<li class='info' style='padding:20px; text-align: center;'>No unread messages</li> ";
									}
									$messages = $notification->getMessage();
									foreach($messages as $message)
									{
										$picture = $user->getProfilePicture('chat', $message['sender_id']);
										$name = $user->getName($message['sender_id']);
										echo "<li class='";
										if($message['read'] == 0)
										{
											echo "messageunread";
										}
										else
										{
											echo "message";
										}
										echo "'><a class='message' href='message?thread=".$message['thread']."&id=".$message['id']."'>
										<div style='display:table-row;'>
										<img class='notification_user_image' src='".$picture."'>
										</img><p style='vertical-align:top; display:table-cell;'><b>".$name."</b><br> ".$extend->trimStr($message['title'], 20)."<br>".$extend->trimStr($message['message'], 20)."</p></div></a></li> ";
									}
									?>
								</ul>
							</div>
						</div>
						<?php 
						if($message_count > 0)
						{
							if($message_count > 20)
							{
								echo'<img class ="message_notification" src="Images/Icons/icons/notification-counter-20-plus.png"></img>';
							} 
							else 
							{
								echo'<img class ="message_notification" src="Images/Icons/icons/notification-counter-'.$message_count.'.png"></img>';
							}
						}?>
					</div>
					<div class="notification" id="notification_click">
						<img style='height:18px;' id="notification" class ="message" src='Images\Icons\Icon Pacs\glyph-icons\glyph-icons\PNG\Network.png'></img><br>
						<div id="notificationdiv" class="popup_div">
							<div class="popup_top">
								<span>Notifications</span>
							</div>
							<div class="popup_content scroll_medium">
								<ul class="notify"> 
									<?php 
									$notify_count = $notification->getNotificationNum(); 
									if ($notify_count == 0)
									{
										echo "<li class='info' style='padding:20px; text-align: center;'>No new notifications</li> ";
									}
									$notifications = $notification->getNotification();
									foreach($notifications as $notify)
									{
										$picture = $user->getProfilePicture("chat", $notify['sender_id']);
										$name = $user->getName($notify['sender_id']);
										echo "<li onclick='markNotificationRead(".$notify['id'].", \"post?id=".$notify['post_id']."\");' class='";
										if($notify['read'] == 0)
										{
											echo "messageunread";
										}
										else
										{
											echo "message";
										}

										if($notify['type'] == 'like' || $notify['type'] == 'dislike')
										{
											echo "'>
											<div style='display:table-row;'>
											<img class='notification_user_image' src='".$picture."'>
											</img><p style='vertical-align:top; display:table-cell;'><b>".$name."</b> voted on your post!</p></div></li> ";
										}
									}
									?>
								</ul>
							</div>
						</div>
						<?php
						if($notify_count > 0)
						{
							if($notify_count > 20)
							{
								echo'<img class ="message_notification" src="Images/Icons/icons/notification-counter-20-plus.png"></img>';
							} 
							else 
							{
								echo'<img class ="message_notification" src="Images/Icons/icons/notification-counter-'.$notify_count.'.png"</img>';
							}
						}
						?>
					</div>
					<div class="network" id="network_click">
						<img  style='padding-top:2px;width:16px;height:16px;' id="network" class ="message" src='Images\Icons\Icon Pacs\typicons.2.0\png-24px/flow-merge.png'></img><br>
						<div id="networkdiv" class="popup_div">
							<div class="popup_top">
								<span>Network</span>
							</div>
							<div class="popup_content scroll_medium">
								<ul class="notify"> 
									<?php 
									$network_count = $notification->getNetworkNum();
									$networks = $notification->getNetwork();
									if($network_count == 0)
									{
										echo "<li class='info' style='padding:20px; text-align: center;'>No Network News</li> ";
									}
									foreach($networks as $network)
									{
										$picture = $user->getProfilePicture("chat", $network['inviter_id']);
										$group_name = $group->getGroupName($network['group_id']);
										$group_id = $network['group_id'];

										echo "<li class='";
										if($network['read'] == 0)
										{
											echo "messageunread";
										}
										else
										{
											echo "message";
										}
										echo "'>
										<div style='display:table-row;'>
										<img class='notification_user_image' src='".$picture."'>
										</img><p style='text-align:left;padding-left:25px;vertical-align:top; display:table-cell;'>".
										str_replace('$group', '"'.$group_name.'"', str_replace('$user', $network['inviter_name'], $phrases['group_invite_en']))."</p>
										<table><tr><td>";
										if($network['invite_status'] == 2)
										{
											echo "<button onclick='leaveGroup(".$group_id.", ".$network['id'].");' 
											class='pure-button-yellow small'>Leave</button>";
										}
										else if($network['invite_status'] == 0)
										{
											echo "<button onclick='joinGroup(".$group_id.", ".$network['id'].");' 
											class='pure-button-primary small'>Join</button>";
										}
										else
										{
											echo "<button onclick='joinGroup(".$group_id.", ".$network['id'].");' 
											class='pure-button-success small'>Join</button></td><td><button onclick='rejectGroup(".$group_id.", ".$network['id'].");'
											class='pure-button-error small'>Reject</button>";
										}
										echo "</td></tr></table></div></li>";
									}
									if ($network_count == 0)
									{
										
									}
									?>
								</ul>
							</div>
						</div>
						<?php
						if($network_count > 0)
						{
							if($network_count > 20)
							{
								echo'<img class ="message_notification" src="Images/Icons/icons/notification-counter-20-plus.png">';
							} 
							else 
							{
								echo'<img class ="message_notification" src="Images/Icons/icons/notification-counter-'.$network_count.'.png">';
							}
						}
						?>
					</div>
				</div>
				<div style="z-index:11;" class="search">
					<input class="search_box" autocomplete='off' style='background-position: 98% 50%; padding-right:35px;background-repeat: no-repeat;
					background-image: url("Images/Icons/Icon Pacs/Batch-master/Batch-master/PNG/16x16/search-2-grey.png");' onkeyup='getnames(this.value);if (event.keyCode == 13) 
					{ $("#match").click(); }' type='text' id='names_input' placeholder='Search...' name='receiver'>
					<div style="display:none" class="search_results scroll_medium" id='names_universal'></div>
				</div>
				<div class="gear">
					<a style = "cursor:pointer;">
						<img id="gear_click" style="z-index:11; width:16px; height:16px; " class="logout_image_small message" src ="Images\Icons\Icon Pacs\Batch-master\Batch-master\PNG\16x16\settings-2.png"></img>
					</a>
					<div style="display:none;" id="geardiv" class="geardiv">
						<ul> 
							<li class="nav_option"><a title"Logout" href="Scripts/logout.php">Logout</a></li> 
							<li class="nav_option"><a href="">Privacy</a></li> 
						</ul> 
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>