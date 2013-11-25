<head>
	<link rel="stylesheet" type="text/css" href="CSS/chat.css">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script src="Scripts/jquery.cookie.js"></script>
	<script id="chat_loader">
	var bottom = true;
	var view = getCookie("showchat");
	$(function()
	{
		scrollToBottom();changeTabColor();
		var cookie = getCookie("showchat");
		if(cookie == 0)
		{
			$('#chat').hide();
		}
		else
		{
			if(view == 's')
			{
				$('#school_tab').trigger('click');
			}
			else if(view == 'y')
			{
				$('#year_tab').trigger('click');
			}
			else
			{
				$('#' + view).trigger('click');
			}
		}
		$("#chat_toggle").click(function() 
		{
			var cookie = getCookie("showchat");
			if(cookie > 0 || isNaN(cookie))
			{
				setCookie('showchat', 0, 5);
				$('#chat_toggle').html("OFF");
				$('#chat').hide('slide',{direction:'right'},1000);
			}
			else
			{
				setCookie('showchat', 'y', 5);
				$('#chat_toggle').html("ON");
				$('#chat').show('slide',{direction:'right', duration:0},1000);
			}
		});

		$.post("Scripts/chat.class.php", {chat: view}, function(response)
		{
			$('#chatreceive').empty();
			$('#chatreceive').append(response);
		});

		var auto_refresh = setInterval(
			function ()
			{
				$.post("Scripts/chat.class.php", {chat: view}, function(response)
				{
					$('#chatreceive').empty();
					$('#chatreceive').append(response);

					if(bottom == true)
					{
						var div = document.getElementById('chatoutput');
						$(div).scrollTop(99999999999);
					}
				});
			}, 2000);
	});
</script>	
<script type="text/javascript">
$(function()
{
	$('#chatoutput').bind('scroll', function()
	{
		if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight)
		{
			bottom = true;
		}
		else
		{
			bottom = false;
		}
	})
}
);

function scrollToBottom()
{
	var div = document.getElementById('chatoutput');
	$(div).scrollTop(99999999999);
}
function scrollIntoView(id)
{
	if(id == "s")
	{
		id = ".school_tab";
		scrollIntoViewHelper(id, true);
	}
	else if(id == "y")
	{
		id = ".year_tab";
		scrollIntoViewHelper(id, true);
	}
	else
	{
		id = "#"+id;
		scrollIntoViewHelper(id, false);
	}
}
function scrollIntoViewHelper(id, beginning)
{
	if(beginning == true)
	{
		$('#chatencase').animate({scrollLeft: 0}, 400);
	}
	else
	{
		var previous_scroll = $('#chatencase').scrollLeft();
		$('#chatencase').scrollLeft(0);
		var left_offset = $(id).offset().left - $('#chatencase').offset().left - $('#chatencase').width() / 2 + $(id).width() / 2;
		$('#chatencase').scrollLeft(previous_scroll);
		$('#chatencase').animate({scrollLeft: left_offset}, 400);
	}
}
function submitchat(chat_text)
{
	$.post("Scripts/chat.class.php", { action : "addchat", aimed: view, chat_text: chat_text}, function(response)
	{
		$('#text').html('');
		$('#chatoutput').scrollTop(99999999999);
	});
}

function change_chat_view(change_view)
{
	view = change_view;
	$.post("Scripts/chat.class.php", {chat: view}, function(response)
	{
		$('#chatreceive').empty();
		$('#chatreceive').append(response);
	});
	changeTabColor();
	scrollIntoView(change_view);
	setCookie('showchat', change_view, 5);
}

function changeTabColor()
{
	if(view == "y")
	{
		$('#year_tab').css('background-color', 'white');
		$('#school_tab').css('background-color', '');
		$('.group_tab').css('background-color', '');
	}
	else if(view == "s")
	{
		$('#school_tab').css('background-color', 'white');
		$('#year_tab').css('background-color', '');
		$('.group_tab').css('background-color', '');
	}
	else
	{
		$('.group_tab').css('background-color', '');
		$('#year_tab').css('background-color', '');
		$('#school_tab').css('background-color', '');
		$('#'+view).css('background-color', 'white');
	}
}
</script>
</head>
<div class="chatoff" id="chat_toggle"><?php if($_COOKIE['showchat'] == '0'){echo 'OFF';}else{echo 'ON';}?></div>
<div class="chatcomplete" id="chat">
	<div id="chatencase" style='padding-bottom:1px;overflow:auto;'>
		<div id='chatheader' class="chatheader">
			<?php
			echo "<div id='school_tab' class='offswitch' onclick='change_chat_view(&quot;s&quot;);'>School</div>";
			echo "<div id='year_tab' class='offswitch' onclick='change_chat_view(&quot;y&quot;);'>Year ".$user->getYear()."</div>";

			$groups = $group->getUserGroups();
			foreach($groups as $single_group)
			{
				echo "<div class='group_tab' id='".$single_group[0]."' title='".$group->getGroupName($single_group[0])."' 
				onclick='change_chat_view(&quot;".$single_group[0]."&quot;);'>".$extend->trimStr($group->getGroupName($single_group[0]), 6)."</div>";
			}
			?>
		</div>
	</div>
	<div class="chat_text">
		<div id="chatoutput" class="chatoutput">
			<table class='chatbox' id="chatreceive"></table>
		</div>
		<div class='text_input_container'>
			<div contenteditable id="text" onkeydown='
			if (event.keyCode == 13) 
			{
				if(event.shiftKey !== true) 
				{
					submitchat($(this).html());
				}
				else{}
			}
			return true;'
			class="chatinputtext"  data-placeholder="Press Enter to send...">
			</div>
		</div>
	</div>
</div>
<script>var myDiv = document.getElementById('chatoutput'); myDiv.scrollTop = myDiv.scrollHeight;</script>