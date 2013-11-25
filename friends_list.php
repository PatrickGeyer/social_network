<head>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script>
	function show_group() {
		$( "#dialog" ).dialog({
			modal: true,
			buttons: [
			{
				text: "Create Group",
				click: function() 
				{
					var cal = $("#group_name").val();
					if(cal != "")
					{
						createGroup();
					}
				}
			}
			]
		});
	}
	</script>
	<script>
	var auto_refresh = setInterval(
		function ()
		{
			$('#friends_load').load('friends_list.php #friends_load');
		}, 5000);
	</script>

	<script>
	function getnamesgroup(value)
	{
		$.post("Scripts/searchbar.php", {search:"group", input_text: value}, function(data){
			$('#names').empty();
			$('#names').append(data);
		});
	}
	</script>
	<script>
	var invited_members = [];
	function addreceivergroup(new_receiver, new_receiver_name)
	{
		var found = $.inArray(new_receiver, invited_members);
		if(found != -1)
		{

		}
		else
		{
			invited_members.push(new_receiver);
			$('#names_input').val("");
			var html = "<tr><td style='min-width:100px;' id = '"+new_receiver+"'> \
			<div class='tag-triangle'></div><div class='added_name'><span style='font-family:century gothic;'>"+new_receiver_name+"</span> \
			<span class='delete_receiver' onclick='removereceiver("+new_receiver+");'>x \
			</span></div></td></tr>";
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
		var group_type = $("#dialog input[type='radio']:checked").val();
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

</head>
<div id='friends_container'>
	<div id="friends_bar" class="scroll_medium">
		<div id="friend_load">
			<div id="friend_on">
				<ul style="width:100%;">
					<?php
					include_once('Scripts/lock.php')
					?>
					<?php
					$query = "SELECT * FROM users WHERE school_id = :user_school_id AND year = :user_year AND online = 1 AND id != :user_id;";
					$query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
					$query->execute(array(":user_id" => $user->getId(), ":user_school_id" => $user->getSchoolId(), ":user_year" => $user->getYear()));
					if(!$query)
					{
						die (print_r($database_connection->errorInfo()));
					}
					while($friends = $query->fetch(PDO::FETCH_ASSOC))
					{
						$valid ="";
						echo "<li class='friend_list_on'><a class='friend_list' style='background-image:url(".$friends['profile_picture_chat_icon'].");' 
						href ='user?id=".urlencode(base64_encode($friends['id']))."'>"
						.$extend->trimStr($friends['name'], 15)."</a></li>";
					}
					if(isset($valid))
					{
					// echo "<script>$('#friend_on').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- online classmates -</small>');</script>";
					}
					?>
				</ul>
			</div>
			<div id = "friend_off">
				<ul style="width:100%;">
					<?php
					$query = "SELECT * FROM users WHERE school_id = :user_school_id AND year = :user_year AND online = 0 AND id != :user_id;";
					$query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
					$query->execute(array(":user_id" => $user->getId(), ":user_school_id" => $user->getSchoolId(), ":user_year" => $user->getYear()));
					if(!$query)
					{
						die ($database_connection->errorInfo());
					}
					while($friends = $query->fetch(PDO::FETCH_ASSOC))
					{
						$valid1 = "";
						echo "<li class='friend_list_off'><a class='friend_list' 
						style='background-image:url(".$friends['profile_picture_chat_icon'].");' href ='user?id=".urlencode(base64_encode($friends['id']))."'>"
						.$extend->trimStr($friends['name'], 15)."</a>
						<ul></ul></li>";	
					}
					if(isset($valid1))
					{
					// echo "<script>$('#friend_off').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- offline classmates -</small>');</script>";
					}
					?>
				</ul>
			</div>
			<div id="group_list">
				<ul style="width:100%;">
					<?php
					$query1 = "SELECT * FROM group_member WHERE member_id = :user_id;";
					$query1 = $database_connection->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
					$query1->execute(array(":user_id" => $user->getId()));
					if(!$query1)
					{
						die ($database_connection->errorInfo());
					}
					while($group_id = $query1->fetch(PDO::FETCH_ASSOC))
					{
						$valid2 = "";
						$query_group = "SELECT * FROM `group` WHERE id = :group_id;";
						$query_group = $database_connection->prepare($query_group, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
						$query_group->execute(array(":group_id" => $group_id['group_id']));
						if(!$query_group)
						{
							die ($database_connection->errorInfo());
						}
						if(!$group_info = $query_group->fetch(PDO::FETCH_ASSOC))
						{
							die (print_r($database_connection->errorInfo()));
						}
						echo "<li class='friend_list_group' >
						<a class='friend_list' style='background-image:url(".$group_info['group_profile_picture_chat'].");'
						href ='group?id=".urlencode(base64_encode($group_info['id']))."'>".$extend->trimStr($group_info['group_name'], 15)."</a></li>";
						$friend_query = "SELECT * FROM group_member WHERE group_id = :group_id AND member_id != :user_id";
						$friend_query = $database_connection->prepare($friend_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
						$friend_query->execute(array(":user_id" => $user->getId(), ":group_id" => $group_id['group_id']));
						while($friends_in_group = $friend_query->fetch(PDO::FETCH_ASSOC))
						{
							$friend_group_query = "SELECT * FROM users WHERE id = :member_id;";
							$friend_group_query = $database_connection->prepare($friend_group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
							$friend_group_query->execute(array(":member_id" => $friends_in_group['member_id']));
							while($friend_profile = $friend_group_query->fetch())
							{
								if($friend_profile['online'] == true)
								{
									echo "<li class='friend_list_on'><a class='friend_list' style='background-image:url(".$friend_profile['profile_picture_chat_icon'].");' 
									href ='user?id=".urlencode(base64_encode($friend_profile['id']))."'>".$extend->trimStr($friend_profile['name'], 15)."</a></li>";
								}
								else
								{
									echo "<li class='friend_list_off'><a class='friend_list' style='background-image:url(".$friend_profile['profile_picture_chat_icon'].");' 
									href ='user?id=".urlencode(base64_encode($friend_profile['id']))."'>".$extend->trimStr($friend_profile['name'], 15)."</a></li>";
								}
							}
						}
					}
					while($friends = $query->fetch(PDO::FETCH_ASSOC))
					{
						if($friends['online'] == true)
						{
							echo "<li class='friend_list_on'><a class='friend_list' style='background-image:url(".$friends['profile_picture_chat_icon'].");' 
							href ='user?id=".urlencode(base64_encode($friends['id']))."'>".$extend->trimStr($friends['name'], 15)."</a></li>";
						}
						else
						{
							echo "<li class='friend_list_off'><a class='friend_list' style='background-image:url(".$friends['profile_picture_chat_icon'].");' 
							href ='user?id=".urlencode(base64_encode($friends['id']))."'>".$extend->trimStr($friends['name'], 15)."</a></li>";					
						}
					}
					if(isset($valid2))
					{
					// echo "<script>$('#group_list').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- group -</small>');</script>";
					}
					?>
				</ul>
			</div>
			<div id="school_list">
				<ul style="width:100%;">
					<?php
					$query = "SELECT * FROM users WHERE school_id = :user_school_id AND year != :user_year;";
					$query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
					$query->execute(array(":user_school_id" => $user->getSchoolId(), ":user_year" => $user->getYear()));
					if(!$query)
					{
						die ($database_connection->errorInfo());
					}
					while($friends = $query->fetch(PDO::FETCH_ASSOC))
					{
						$valid3 = "";
						if($friends['online'] == true)
						{
							echo "<li class='friend_list_on'><a class='friend_list' style='background-image:url(".$friends['profile_picture_chat_icon'].");' 
							href ='user?id=".urlencode(base64_encode($friends['id']))."'>".$friends['name']."</a></li>";
						}
						else
						{
							echo "<li class='friend_list_off'><a class='friend_list' style='background-image:url(".$friends['profile_picture_chat_icon'].");' 
							href ='user?id=".urlencode(base64_encode($friends['id']))."'>".$friends['name']."</a></li>";					
						}
					}
					if(isset($valid3))
					{
					// echo "<script>$('#school_list').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- school -</small>');</script>";
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	<button style='margin-top:10px;' class='pure-button-primary small' onclick='show_group();' title='Create a Group'>Create Group</button>
</div>
</div>


<div hidden id="dialog" title="Create a Group" style="display:none;overflow:hidden;">
	<div class="pseudonym">
		<table class="none" style="width:100%;">
			<tr>
				<td>
					<input style="border-radius:0; width:100%;" id='group_name' type="text" autocomplete="off" 
					onkeyup="if(this.value == ''){$(&#39;#group_warning&#39;).fadeIn();}else{$(&#39;#group_warning&#39;).hide();}" placeholder="Group Name" />
				</td>
				<td hidden id="group_warning"><div class="warning_red">Group name cannot be blank!</div>
				</td>
			</tr>
			<tr>
				<td><textarea style="border-radius:0; width:100%; height:100px;" id = 'group_about' class="thin" style="width:100%; height: 100px;" placeholder="About..." autocomplete="off" type="text"></textarea></td>
			</tr>
			<tr>
				<td>
					<div style="border-radius:0;">
						<hr>
						<input type="radio" name="group_type" value="public" />Public<br><em style="font-size:10px;"> Anyone can view the group.</em><hr style="margin:0;">
						<input checked type="radio" name="group_type" value="school" />School<br><em style="font-size:10px;"> Only school members can view the group.</em><hr style="margin:0;">
						<input type="radio" name="group_type" value="seccret" />Secret<br><em style="font-size:10px;"> No one except members know of the group.</em><hr style="margin:0;">
						<hr>
					</div>
				</td>
			</tr>
		</table>
		<table class="none" style='width:100%;'>
			<tr>
				<td><input autocomplete='off' style='border-radius:0; width:100%;' onkeyup='getnamesgroup(this.value);' type='text' id='names_input' placeholder='Add Member...' /></td>
			</tr>
			<tr>
				<td id ='names_slot'></td>
			</tr>
		</table>
		<div hidden class="scroll_medium" style='overflow:auto;max-height:100px; position:relative; padding:2px;border: 1px solid lightgrey; background-color:white;' id='names'>
		</div>
	</div>
</div>