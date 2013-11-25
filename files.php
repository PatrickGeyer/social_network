<?php
include_once('Scripts/lock.php');
$page_identifier = 'files';
if(isset($_COOKIE['current_directory']))
{
	$current_dir = $_COOKIE['current_directory']."/";
}
else
{
	$current_dir = "User/Files/".$user->getId()."/";
}

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}
if($_SERVER['REQUEST_METHOD'] == "POST")
{	
	if(isset($_POST['action']) && $_POST['action'] == 'delete') 
	{
		if(!is_dir(urldecode($_POST['filepath'])))
		{ 
			unlink(urldecode($_POST['filepath']));
		}
		else
		{
			rrmdir(urldecode($_POST['filepath']));
		}
	}
}

function findexts ($filename) {
	$filename = strtolower($filename);
	$exts = explode(".", $filename);
	if(sizeof($exts) <= 1 )
	{
		return "";
	}
	else
	{
		$n = count($exts)-1;
		$exts = $exts[$n];
		return $exts;
	}
}

function stripexts ($filename) 
{
	$filename = strtolower($filename);
	$exts = explode(".", $filename);
	return $exts[0]; 
}

?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/files.css">
	<script src="Scripts/jquery.form.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script src="Scripts/jquery.cookie.js"></script>
	<title>My Files</title>
	<script>
	function scrollToElement()
	{
			y = document.height; //2613
			window.scrollBy(0,y);
		}
		</script>
		<script name ="uploadFiles">
		function _(el)
		{
			return document.getElementById(el);
		}
		var q = 0;
		function uploadFile()
		{
			var files = _("file").files;
			var length = files.length;
			for(var count = 0; count < length; count++)
			{
				q++;
				$("#progress_bar_holder").empty();
				$("#progress_bar_holder").append("<progress id='progressBar' value='0' max='100' style='width:100%;''></progress>");
				$("#progress_bar_holder").append("<span id='status'></span>");
				var file = files[count];
				var dir = "<?php echo $current_dir; ?>";
				var formdata = new FormData();
				formdata.append("file", file);
				formdata.append("dir", current_directory);
				var xhr = new XMLHttpRequest();
				xhr.upload.onprogress = function(event) {progressHandler(event, count);};
				xhr.onload = function () {completeHandler(this, count-1);};
				xhr.addEventListener("error", errorHandler, false);
				xhr.addEventListener("abort", abortHandler, false);
				xhr.open("post", "Scripts/upload_file.php");
				xhr.send(formdata);
			}
		}
		function progressHandler(event, id)
		{
			var percent = (event.loaded / event.total) * 100;
			percent = Math.round(percent);
			$("#progressBar").val(percent);
			if(q > 1)
			{
				$("#status").text(q+" items uploading...");
			}
			else
			{
				$("#status").text(q+" item uploading... " + percent +"%");
			}
		}
		function completeHandler(event, id)
		{
			if(q > 1)
			{
				q--;
			}
			else
			{
				$("#status").text("Upload Successful!");
				$("#progressBar").hide();
				$("#status").fadeOut(5000);
			}
			refreshCurrentDiv();
		}
		function errorHandler(event)
		{
			if(q > 1)
			{
				q--;
			}
			_("status").innerHTML = "Upload Failed!";
		}
		function abortHandler(event)
		{
			if(q > 1)
			{
				q--;
			}
			_("status").innerHTML = "Upload Aborted!";
		}
		function refreshCurrentDiv()
		{
				if('User/Files/<?php echo $user->getId(); ?>////'.indexOf(current_directory) != -1)
				{
					$('#file_container').empty();
					get_folder_contents($('#file_container'), 'action', current_directory, 'none');
					console.log(" home");
				}
				else
				{
					console.log("not home");
					$('#main_file').find('div').each(function()
					{
						if($(this).attr('path')+"/" == current_directory || $(this).attr('path')+"//" == current_directory)
						{
							$(this).find('div').remove();
							get_folder_contents($(this), 'action', current_directory, 'none');
						}
					});
				}
		}
		function get_folder_contents(element, action, dir, animation)
		{
			if(action == "remove")
			{
				$(element).children('div').slideUp( function(){});
				var new_onclick = $(element).attr('previous_onclick');
				$(element).attr('onclick', new_onclick);
				$.post('Scripts/current_directory_cookie.php', {dir:"../"}, function(response)
				{	
					current_directory = response;
				});
			}
			else
			{
				$.post('Scripts/get_folder_contents.php', {dir:dir}, function(response)
				{	
					var previous_onclick = $(element).attr("onclick");
					$(element).attr("previous_onclick", previous_onclick);
					$(element).attr("onclick", "if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;get_folder_contents(this, 'remove');");
					if(animation != "none")
					{
						$("<div style='padding:6px;'></div>" + response).appendTo($(element)).hide().slideToggle();
					}
					else
					{
						$("<div style='padding:6px;'></div>" + response).appendTo($(element));
					}
				});
				$.post('Scripts/current_directory_cookie.php', {dir:dir}, function(response)
				{	
					current_directory = response;
				});
			}
		}
		function deleteFile(element, filepath, folder)
		{
			$.post('../files.php', {action:"delete", filepath: filepath}, function(response)
			{
				if($(element).parent().attr('class') == "folder")
				{
					var folder_container = $(element).parents('.folder');
					$(element).parents('.folder').children('div').each(function()
					{
						$(this).remove()
					});

					get_folder_contents(folder_container, 0, folder, "none");
				}
				else
				{
					$(element).parent().parent().slideUp();
				}
			});
		}

		function createFolder()
		{

			var folder_name = $('#creat_folder_name').val();
			var path = current_directory;
			$.post("Scripts/create_folder.php", {current_directory: path, folder_name: folder_name}, function(response)
			{
				if("error".indexOf(response) > 0)
				{
					alert("error!");
				}
				else
				{
					setTimeout(function() {
		       			refreshCurrentDiv();
		   			}, 1000);
				}
			});
		}
		</script>

	</head>
	<body>
		<?php include_once('welcome.php');
		include_once('friends_list.php');
		include_once('chat.php');
		
		$nofilepath = "User/Files/nothumpnail.png";
		if(isset($current_dir))
		{
			$personaldir = $current_dir;
		}
		else
		{
			$personaldir = "User/Files/".$user->getId();
		}
		if(is_file($personaldir))
		{
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
		}
		?>
		<?php include_once('chat.php');?>
		
		<div class="container" id="files">
			<div id="create_folder" style="cursor:pointer;">
				<button style='position:absolute;top:40px;right:20px;' class='pure-button-secondary small'>Create Folder</button>
				<br />
			</div>
			<div title="Create Folder" id="create_folder_dialog" hidden style="width:100%;">
				<input id="creat_folder_name" type="text" style="width:100%;" name="folder_name" placeholder="Folder Name..." />
				<input id="path" hidden value="<?php echo $current_dir ?>" />
			</div>
			<h1 style="visibility:hidden;margin:0;">Files</h1>

			<div id="upload_file" style="cursor:pointer;height:50px;position:absolute;top:40px;right:150px;">
				<button class='pure-button-success small' onclick="$('#file').trigger('click');">Upload File</button>
			</div>
			<div id="upload_file_dialog" style="position:absolute;top:40px;right:150px; cursor:pointer; display:none;">
				<table>
					<tr>
						<td>
							<input style='float:left;' type="file" name="file" id="file" multiple>
						</td>
						<td>
							<button style='height:35px;font-size:12px;float:right;' id="upload" class="pure-button-success" onclick="uploadFile();">Upload</button>
						</td>
					</tr>
				</table>
			</div>

			<div id='main_file' class="file" style='border:0;position:relative;'>
				<div id="file_container" class='folder' style="border:0px;position:relative;background-color:transparent; padding:0px;">
				</div>
			</div>

			<hr style="padding-top:50px; visibility:hidden;">
			<div id = "progress_bar_holder"></div>

			<script>
			$(function()
			{
				get_folder_contents($('#file_container'), 0, "<?php echo $personaldir; ?>");
			});
			$('#upload_file').click(function(){
				$('#upload_file').hide();
				$('#upload_file_dialog').fadeIn();
			});

			$('#create_folder').click(function(){
				//$('#create_folder').hide();
				$('#create_folder_dialog').dialog(
					{ buttons: [ { text: "Create Folder", click: function() { $( this ).dialog( "close" ); createFolder();} } ] });
			});
			</script>
			<button onclick='dialog()'>Dia</button>
		</body>
		</html>