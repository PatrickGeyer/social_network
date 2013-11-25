<?php
include_once('Scripts/lock.php');
$page_identifier = "home";
?>
<!DOCTYPE html>
<html>
<head>	
	<link rel="stylesheet" type="text/css" href="CSS/home.css">
	<script src="https://code.jquery.com/jquery-latest.min.js"></script>

	<script>
	$('.icon').click(function() {
		$('.icon').fadeTo('slow', 0.5);
	});
	function openDialog(){
		$( "#status_image_selector" ).click();
	}
	</script>
	<script>

	</script>
	<script>
	var share_with = <?php 
	if(isset($_GET['filterg']))
	{
		echo '"group";';
		$group_id = $_GET['filterg'];
		echo "var group_id = ".$group_id.";";
	}
	else if(isset($_GET['filterschool']))
	{
		echo '"school";';
	}
	else
	{
		echo '"school";'; 
			//change to all!!!!!!
	}
	?>
	</script>
	<script>
	$(function ($) 
	{
		$("#file").change(function(e) 
		{ 
			validateFiles();
		});
	});

	</script>

	<script>
	var q = 0;
	var files;
	var length;
	function validateFiles()
	{
		files = $("#file")[0].files;
		length = files.length;
		uploadFile(0);
	}
	function uploadFile(count)
	{
		q++;
		$("#progress_bar_holder").empty();
		$("#progress_bar_holder").append("<progress id='progressBar' value='0' max='100' style='width:100%;'></progress>");
		var file = files[count];
		var dir = "User/Files/<?php echo $user->getId();?>/Posts/";
		var formdata = new FormData();
		formdata.append("file", file);
		formdata.append("dir", dir);
		var xhr = new XMLHttpRequest();
		xhr.upload.onprogress = function(event) {progressHandler(event, count);};
		xhr.onload = function () {completeHandler(this, count, dir, file);};
		xhr.addEventListener("error", errorHandler, false);
		xhr.addEventListener("abort", abortHandler, false);
		xhr.open("post", "Scripts/upload_file.php");
		xhr.send(formdata);
	}
	function progressHandler(event, id)
	{
		var percent = (event.loaded / event.total) * 100;
		percent = Math.round(percent);
		$("#progressBar").val(percent);
	}
	function completeHandler(event, id, dir, file)
	{
		$('#progressBar').remove();
		var textarea = $('#status_text');
		var filename = $("#file")[0].files[id].name;
		textarea.focus();
		$('#status_text').append("<img src='"+dir+filename+"'></img>");
		$('#status_text').css('height', '500px');
		$('#status_text').css('height', 'auto');
		check_loop(id+1);
	}
	function errorHandler(event)
	{
		alert("fail");
	}
	function abortHandler(event)
	{
		alert('abort');
	}
	function check_loop(id)
	{
		if(id <= length)
		{
			uploadFile(id);
		}
	}
	</script>
	<title>Home</title>
	<link href="Scripts/video-js/video-js.css" rel="stylesheet">
	<script src="Scripts/video-js/video.js"></script>
	<script>
	_V_.options.flash.swf = "Scripts/video-js/video-js.swf"
	</script>
</head>
<?php 
include_once('welcome.php');
include_once('friends_list.php'); 
include_once('chat.php');
?>
<body style="overflow:auto;">
	<div class="container_home" id="home_container">
		<div class='updateStatus' style="padding-top:20px;">
			<table style="width:100%;">
				<tr>
					<td style="width: 40px;">
						<button onclick="submitPost();" class="pure-button-success status_button">POST</button>
					</td>
					<td>
						<div contentEditable='true' id="status_text" name="status_text" data-placeholder= "Update Status..." class="status_text scroll_medium"></div>
					</td>
					<td id="upload_td" style="width:50px;">
						<input id="file" name="file" type="file" multiple style="right:15px;opacity:0;top:20px;position:absolute;width:50px;height:50px;cursor:pointer;z-index:10;"></input>
						<span style="font-size:2em;color:grey;position:absolute;right:30px;top:25px;">+</span>
					</td>
				</tr>
			</table>	
			<div style="width:100%" id="progress_bar_holder"></div>
			<center>
				<div> <!--opening the filter container div -->
					<h3 class="nav">All</h3><img class="nav" style="opacity:0.5;" src="Images/down.png"></img>
					<ul class="select" style="  
					width: 250px;
					color: #74646e;
					border: 1px solid #C8BFC4;
					border-radius: 4px;
					background-color: #fff; 
					margin-top:20px;" id="filter_select">
					<li class='filter' data-value="all">All</li>
					<?php 
					if(isset($_GET['filterschool']) && $_GET['filterschool'] == "school")
					{
						echo "<li class='filter' data-value='school'>".$user->getSchool()."</li>";
						echo "<script>$('.nav').text('".$user->getSchool()."');</script>";
					}
					else if(!isset($_GET['filterschool']))
					{
						echo "<li class='filter' data-value='school'>".$user->getSchool()."</li>";
					}
					$usergroups = $group->getUserGroups();
					foreach($usergroups as $group_id)
					{
						if(isset($_GET['filterg']) && $_GET['filterg'] == urldecode($group_id[0]))
						{
							echo "<li class='filter' data-value='group^".urlencode($group_id[0])."'>".$group->getGroupName($group_id[0])."</li>";
							echo "<script>$('.nav').text('".$group->getGroupName($group_id[0])."');</script>";
						}
						else
						{
							echo "<li class='filter' data-value='group^".urlencode($group_id[0])."'>".$group->getGroupName($group_id[0])."</li>";
						}
					}
					?>
				</ul>
			</div>
		</center> 
		<!-- <button class="pure-button-success">Open collaborative Text</button> -->
		<!-- close filter div -->
	</div> <!--close post status div -->
		<!-- <video id="example_video_1" class="video-js vjs-default-skin"  
		controls preload="auto" width="640" height="264"  
		data-setup='{"example_option":true}'>
		<source src="sample.mp4" type="video/mp4">
		</video> -->
		<?php
		if(isset($_GET['filterg']))
		{
			$activity_query = "SELECT * FROM activity WHERE 
			id IN (SELECT activity_id FROM activity_share WHERE group_id = :group_id)
			ORDER BY time DESC";
			$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$activity_query->execute(array(":group_id" => urldecode($_GET['filterg'])));
		}
		else if(isset($_GET['filterschool']))
		{
			$activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
				school_id = :school_id) ORDER BY time DESC";
		$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$activity_query->execute(array(":school_id" => $user->getSchoolId()));
		}
		else
		{					
			$activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
				school_id = :school_id OR 
				(year = :user_year AND school_id = :school_id) OR group_id in 
				(SELECT group_id FROM group_member WHERE member_id = :user_id) OR
				receiver_id = :user_id) ORDER BY time DESC";
		$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$activity_query->execute(array(":user_id" => $user->getId(), ":school_id" => $user->getSchoolId(), ":user_year" => $user->getYear()));
		}

		if(!$activity_query)
		{
			die("Error:".$database_connection->errorInfo());
		}
		$count = $activity_query->rowCount();
		while($activity = $activity_query->fetch(PDO::FETCH_ASSOC))
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

		if($count == 0)
		{
			echo "<hr><center><span style='font-family: century gothic;'>You have no notifications in this Live Feed!</span></center>";
		}
		else
		{
						echo '</div>'; // close container div
					}
					?>	
					<script>
					function showhide(element)
					{
						$(element).toggle( "slide" );
					});
</script>
<script>
var nav = $('.nav');
var selection = $('.select');
var select = selection.find('li');

nav.click(function(event) {
	if (nav.hasClass('active')) {
		nav.removeClass('active');
		selection.stop().slideUp(200);
	} else {
		nav.addClass('active');
		selection.stop().slideDown(200);
	}
	event.preventDefault();
});

select.click(function(event) {
    // updated code to select the current language
    select.removeClass('active');
    $(this).addClass('active');

    var value = $(this).attr('data-value');
    var split = value.split('^');
    if(value.indexOf('s') >= 0)
    {
    	window.location.replace("home?filterschool="+split[0]);
    }
    else if(value.indexOf('group') >= 0)
    {
    	window.location.replace("home?filterg="+split[1]);
    }
    else
    {
    	window.location.replace("home");
    }
});
</script>
</body>
</html>