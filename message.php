<?php
include_once('Scripts/lock.php');
$page_identifier = "inbox";
include_once('welcome.php');
include_once('chat.php');

if(isset($_GET['thread']))
{
	$messagethread = $_GET['thread'];
	$notification->markMessageRead("thread" , $messagethread);
	$querymesthread = $database_connection->query("SELECT * FROM messages WHERE thread = ".$messagethread." AND receiver_id = ".$user->getId().";");
	$database_connection->query("UPDATE message_read SET `read` = 1, seen=1 WHERE receiver_id =".$user->getId()." AND thread_id = ".$messagethread.";");
}

if(isset($_GET['id'])) 
{
	$messageid = $_GET['id'];
	$notification->markMessageRead("id" , $messageid);
	if(!$database_connection->query("UPDATE message_read SET `read` = 1, seen=1 WHERE id = ".$messageid.";"))
	{
	}

}

if(isset($_POST['action']) && $_POST['action'] == 'delete') 
{
	if(!mysql_query("DELETE FROM messages WHERE thread = ".$_POST['message_id'].";"))
	{
		echo "error/".mysql_error();
	}
	else
	{
		die("success/".$_POST['message_id']);
	}
}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/message.css">
	<script src="Scripts/jquery.cookie.js"></script>
	<script>var myDiv = document.getElementById('chatoutput'); myDiv.scrollTop = myDiv.scrollHeight;</script>
	<title>Inbox</title>
	<script>

	function getnamesmessage(value)
	{
		$.post("Scripts/searchbar.php", {search:"message", input_text: value}, function(data){
			$('#names').empty();
			$('#names').append(data);
		});
	}

	$(document).click(function(e)
	{
		$("#names").hide();	
	       	});	// ***ADD*** if element is input do nothing!
	$(document).keypress(function(e)
	{
		if(e.which == 13) 
		{
			$('#match').click();
		}
	});

	var receivers = [];
	function addreceivermessage(new_receiver, new_receiver_name)
	{
		var found = $.inArray(new_receiver, receivers);
		if(found != -1)
		{

		}
		else
		{
			receivers.push(new_receiver);
			$('#names_input').val('');
			var html = "<td style='min-width:100px;' id = '"+new_receiver+"'> \
			<div class='added_name'><span style='font-family:century gothic;'>"+new_receiver_name+"</span> \
			<span class='delete_receiver' onclick='removereceivermessage("+new_receiver+");'>x \
			</span></div></td>";
			$('#names_slot').before(html);
		}
	}
	function removereceivermessage(receiver_id)
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
		var title = $('#1title').val();
		var message = $('#1message').val();
		$.post("Scripts/extends.class.php", { action : "sendMessage", reply: reply, message: message, title: title, receivers: receivers}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				window.location.replace("message?thread=" + status[1]);
			}
			else
			{
				alert('error' + response);
			}
		});
	}
	</script>
	<script>
	$( document ).ready(function() {
		$(".delete").hide();
		$( "#deletebutton" ).click(function() {
			$(".delete").toggle( "slide" );
		});
	});
	function deleteMessage(message_id)
	{
		$.post("message.php", { action: "delete", message_id : message_id}, function(response)
		{
			var status = response.split("/");
			if(status[0] == "success")
			{
				$('#' + message_id).slideUp();
			}
		});
	}
	</script>
</head>
<body>
	<div class="messagecomplete">
		<div id="message" class="messagehi">
			<div id= "messagetoolbox" class="messagetoolbox">
				<a class="none" href="message"><button title="Compose a message" alt='Compose a message' class="pure-button-primary small">Compose</button><a/>
					<button id='deletebutton' class="pure-button-error small">Delete</button>
					<a href='message'><button id='deletebutton' class="pure-button-secondary small">Refresh</button></a>
				</div>
				<div style='border:0;max-height:65%;border-right:1px solid lightgrey;' class="scroll_medium">
					<ul class="inboxmessage">
						<?php 
						$threadcount = array();
						$allMessages = $notification->getMessage();
						$messagecount = 0;
						foreach($allMessages as $resultmes)
						{
							$messagecount++;
							$threadnumber = $resultmes['thread'];
							if(!in_array($threadnumber, $threadcount))
							{	
								$user_profile = "SELECT * FROM users WHERE id=".$resultmes['sender_id'].";";
								$user_profile = $database_connection->prepare($user_profile);
								$user_profile->execute();
								$user_profile= $user_profile->fetchAll();
								if($resultmes['read'] == 0)
								{
									echo "<li onclick='window.location.assign(&quot;message?thread=".$resultmes['thread']."&quot;);' id='".$resultmes['thread']."' class='inboxmessage'>";
								}
								else
								{
									echo "<li onclick='window.location.assign(&quot;message?thread=".$resultmes['thread']."&quot;);' id='".$resultmes['thread']."' class='inboxmessageread'>";
								}
								echo "<img style='float:left;margin-right:10px;margin-top:3px;' src='".$user_profile[0]['profile_picture_chat_icon']."'></img>
									<div style='margin-left:50px;'><span class='user_name'>".$user_profile[0]['name']."</span><br /> <span id='title'>".$extend->trimStr($resultmes['title'], 20)."</span>
									<br><span>".$extend->trimStr($resultmes['message'],40)."</span></div>
									<button class='pure-button-error small delete' hidden onclick='deleteMessage(".$threadnumber.");'>Delete</button>
									</li>";
							}
							array_push($threadcount, $threadnumber);		
						}

						if ($messagecount == 0)
						{
							echo "<li style='padding:20px; text-align: center;'>No messages</li> ";
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<?php 
		
		if(!isset($messagethread))
		{
			echo "
			<div class='container' style='top:0px;padding-top:30px;bottom:10px;' id='compose'>
			<table style='max-width:800px;'><tr><td id='names_slot'>
			<input autocomplete='off' style='border-radius:0;' onkeyup='getnamesmessage(this.value);' type='text' id='names_input' placeholder='To...'></td></tr></table>
			<div hidden style='overflow:auto;max-height:200; position:absolute; min-width:350px;padding:2px;border: 1px solid lightgrey; background-color:white;' id='names'></div>

			<input id='1title' style='border-radius:0;' placeholder='Title...' autocomplete='off' name='title' type='text'>
			<textarea id='1message' placeholder='Message...' class='thin messagebox' type='text'></textarea><br/><br/>
			<button class='pure-button-success' onclick='sendMessage();'>Send</button>
			
			</div>
			";
		}
		else
		{
			echo "
			<div class='container' style='max-width:1025px;padding-bottom:250px;padding-top:50px;top:0;padding-left:0;' id='compose'>
			<div class='compose' id='compose'>
			<div class='text'>
			<table class='message'>
			";
			foreach($querymesthread as $message)
			{
				$sql = "SELECT * FROM `users` WHERE id=".$message['sender_id'].";";
				$sql = $database_connection->prepare($sql);
				$sql->execute();
				$message_user = $sql->fetchAll(PDO::FETCH_ASSOC);

				echo"
				<tr class ='message'>
				<td style='width:60px;'><img style='height:50px;width:50px;' src='".$message_user[0]['profile_picture_chat_icon']."'></img></td>
				<td style='padding-top:10px;'><a href='user?id=".urlencode(base64_encode($message['sender_id']))."'>".$message_user[0]['name']."<a/>:<br />
				<em>".$message['title']."</em>
				<br />".$message['message']."</td>
				<td style='width:200px;'>".$message['time']."
				</td> 
				</tr>";
			}
			if(isset($messageid))
			{
				$querymesid = "SELECT * FROM messages WHERE id = ".$messageid." AND receiver_id = ".$user->getId().";";
				
				$message2 = $querymesid;
			}
			else
			{
				$querymesid = "SELECT * FROM messages WHERE thread = ".$messagethread." AND receiver_id = ".$user->getId()." ORDER BY thread ASC LIMIT 1;";

				$message2 = $querymesid;
			}
			echo"
			</table>
			</div>
			<div class='reply'>
			<table class='reply'>
			<tr>
			</tr>
			<tr>
			<td><textarea class='thin' style='height:100px; width:100%;' name='message' id='styled' placeholder = 'Reply...';></textarea></td>
			</tr>
			<tr>
			<td><input type='submit' onclick='sendMessage();' value='Send'></td>
			</tr>
			</table>
			</div>
			</div>";
			// 			<td hidden>To: </td><td hidden><input type='text' name='receiver' value='".$message2['sender']."''></td>
			// <td hidden></td><td><input name='title' value='".$message2['title']."' type='text' hidden></td>
			// <td hidden></td><td><input name='reply' value='true' type='text' hidden></td>
		}?>	
	</body>
	</html>