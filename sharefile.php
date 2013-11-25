<?php
include_once('welcome.php');
include_once('chat.php');
$file_name = $_GET['filename'];
$file_path = $_GET['filepath'];
?>
<head>
	<title><?php echo $file_name; ?></title>
	<link rel="stylesheet" type="text/css" href="CSS/message.css">
	<script>
	function getnamesgroup()
	{
		var value = $('#names_value').val();
		$.post("Scripts/searchbar.php", {search:"share", input_text: value}, function(data){
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
	function addreceivershare(type, new_receiver, new_receiver_name)
	{
		var found = $.inArray(new_receiver, receivers);
		if(found != -1)
		{

		}
		else
		{
			if(type == "user")
			{
				receivers.push("user/" + new_receiver);
			}
			if(type == "group")
			{
				receivers.push("group/" + new_receiver);
			}
			if(type == "school")
			{
				receivers.push("school/" + new_receiver);
			}
			$('#names_value').val('');
			var html = "<div id='"+new_receiver+"' class='added_name'><span style='font-family:century gothic;'>"+new_receiver_name+"</span> \
			<span class='delete_receiver' onclick='removereceivershare("+new_receiver+");'>x \
			</span></div>";
			$('#names_value').before(html);
			$('#names').hide();
		}
	}
	function removereceivershare(receiver_id)
	{
		var index = receivers.indexOf(receiver_id);
		if (index > -1) 
		{
			receivers.splice(index, 1);
		}
		$('#' + receiver_id).remove();
	}
	</script>
	<script>
	function share()
	{
		var comment = $('#comment').val();
		var path = "<?php echo $file_path; ?>";
		var name = "<?php echo $file_name; ?>";
		$.post("Scripts/share_file.php", { file_path: path, file_name: name, comment: comment, receivers: receivers} , function(response)
		{
			var status = response.split("/");
			alert(response);
		});
	}
	</script>
</head>
<div style="padding-top:50px;" class="container">
	<p><em><strong>Share </strong><?php echo $file_name; ?></em></p>
	<div style="margin-left:50px;">
		<input id="names_value" autocomplete='off' style="width:220px;" onkeyup='getnamesgroup();' type='text' placeholder="With..." class='names_input' />
		<div hidden style='overflow:auto;max-height:200; position:absolute; 
		min-width:350px;padding:2px;border: 1px solid lightgrey; background-color:white;' id='names'></div>
		<hr>
		<textarea style="width:100%;" class="thin" id="comment" placeholder="Comment..."></textarea>
		<hr>
		<center>
		<button onclick="share();">Share</button>
	</center>
	</div>
</div>