<?php
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	include_once('Scripts/lock.php');

	$group_name = $_POST['group_name'];
	$group_about = $_POST['group_about'];
        if(isset($_POST['group_type'])) {
            $group_type = $_POST['group_type'];
        } else {
            $group_type = 'public';
        }
	if(isset($_POST['invited_members']))
	{
		$invited_members = $_POST['invited_members'];
	}
	if(strtolower($group_type) == "secret")
	{
		$group_type = 0;
	}
	else if(strtolower($group_type) == "school")
	{
		$group_type = 1;
	}
	else if(strtolower($group_type) == "public")
	{
		$group_type = 2;
	}
	$database_connection->beginTransaction();
	$group_query = "INSERT INTO `group` (group_founder_id, group_name, group_about, group_type) VALUES (:user_id, :group_name, :group_about, :group_type);";
	$group_query = $database_connection->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$group_query->execute(array(":user_id" => base64_decode($_COOKIE['id']), ":group_name" => $group_name, ":group_about" => $group_about, ":group_type" => $group_type));

	$new_group_id = $database_connection->lastInsertId();
	$group_query = "INSERT INTO `group_member` (member_id, group_id) VALUES (:user_id, :new_group_id);";
	$group_query = $database_connection->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$group_query->execute(array(":user_id" => $user->getId(), ":new_group_id" => $new_group_id));

	$database_connection->commit();

	if(isset($invited_members))
	{
		foreach($invited_members as $member)
		{
			$group_query = "INSERT INTO `group_invite` (inviter_id, inviter_name, receiver_id, group_id) VALUES (:user_id, :user_name, :member_id, :new_group_id);";
			$group_query = $database_connection->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			if(!$group_query->execute(array(":user_id" => $user->getId(), ":user_name" => $user->getName(), ":member_id" => $member, ":new_group_id" => $new_group_id)))
			{
				die("error/".$database_connection->errorInfo());
			}
		}
	}
	die ("success/".urlencode(base64_encode($new_group_id)));
}
?>





















<!--<html>
<head>	
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<title>Create a Group</title>
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
	$(document).click(function(e)
	{
		$("#names").hide();	
	       	});	// ***ADD*** if element is input do nothing!
	$(document).keypress(function(e)
	{
		if(e.which == 13) 
		{
			e.preventDefault();
			$('#match').click();
		}
	});
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
			alert(response);
			//var status = response.split("/");
			//if(status[0].indexOf("success"))
			//{
			//	window.location.replace("group?id=" + status[1]);
			//}
		});
	}
	</script>
</head>
<body>-->
	<?php// include_once('welcome.php');?>
	<?php// include_once('chat.php');?>
	<!--<center>
		<div class="container">
			<h1 class="myheader">Create a Group</h1>
			<div class="pseudonym">
				<table class="none" style="width:100%;">
					<tr>
						<td><input id = 'group_name' type="text" autocomplete="off" name="pseudo" onkeyup="if(this.value == ''){$(&#39;#group_warning&#39;).fadeIn();}else{$(&#39;#group_warning&#39;).hide();}" placeholder="Group Name"></td>
						<td hidden id="group_warning"><div class="warning_red">Group name cannot be blank!</div></td>
					</tr>
					<tr>
						<td><textarea id = 'group_about' class="thin" style="width:100%; height: 100px;" placeholder="About" autocomplete="off" type="text"></textarea></td>
					</tr>
					<tr>
						<td><div class="styled-select"><select style="width:100%;" id='group_type'>
							<option selected>Open</option><option>Invite Only</option><option>School</option><option>Private</option>
						</select></div>
					</td>
				</tr>
			</table>
			<table style='max-width:100%;'>
				<tr>
					<td id ='names_slot'>
						<input autocomplete='off' style='border-radius:0;' onkeyup='getnamesgroup(this.value);' type='text' id='names_input' placeholder='Add Member'>
					</td>
				</tr>
			</table>
			<div hidden style='overflow:auto;max-height:200; position:relative; min-width:350px;padding:2px;border: 1px solid lightgrey; background-color:white;' id='names'></div><br/>
			<hr>
			<input onclick='var cal = $("#group_name").val();if(cal != ""){createGroup();}' type="submit" value="Create Group"><br/>	
		</div>
	</div>
</div>
</div>
</center>
</body>
</html>