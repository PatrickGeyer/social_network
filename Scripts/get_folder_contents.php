<?php
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

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$pdir = $_POST['dir']."/";
	$dir = "../".$pdir;
	$nmr = 0;
	$dircount = new DirectoryIterator($dir);
	foreach($dircount as $file )
	{
  		$nmr += 1;
	}
	$result1 = scandir($dir);
	echo "<div>";
	foreach($result1 as $result)
	{
		if($result != "" && $result != "." && $result != "..")
		{
			$extn = findexts($result); 
			switch ($extn)
			{
				case "png": $extn = "PNG Image"; 	break;
				case "jpg": $extn = "JPEG Image"; 	break;
				case "svg": $extn = "SVG Image"; 	break;
				case "gif": $extn = "GIF Image"; 	break;
				case "ico": $extn = "Windows Icon"; break;

				case "txt": $extn = "Text File"; 	break;
				case "log": $extn = "Log File"; 	break;
				case "htm": $extn = "HTML File"; 	break;
				case "php": $extn = "PHP Script"; 	break;
				case "js":  $extn = "Javascript"; 	break;
				case "css": $extn = "Stylesheet"; 	break;
				case "pdf": $extn = "PDF Document"; break;
				case "docx":$extn = "WORD Doc";		break;

				case "zip": $extn = "ZIP Archive"; 	break;
				case "bak": $extn = "Backup File"; 	break;

				case "mp3": $extn = "Audio"; 		break;
				case "wav": $extn = "Audio"; 		break;
				case "m4a": $extn = "Audio"; 		break;

				case "": 	$extn = "Folder";		break;

				default: 	$extn = strtoupper($extn)." File"; break;
			}
			if($extn == "Folder")
			{
				echo "<div id='".($pdir.$result)."'
				onclick='if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;
				get_folder_contents(this, 0, &quot;".($pdir.$result)."&quot;);' class='folder' path='".$pdir.$result."'>";
			}
			// else if($extn == "PDF Document")
			// {
				// echo "<div class='files' onclick='document.location.assign(&quot;viewer?&quot;);'>";
			// }
			else 
			{		
				echo "<div path='".$pdir."' class='files' onclick='if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;'>";
			}

			echo "<span class='files'>".stripexts($result)."</span>";

			//echo "<span class='files'>".$extn."</span>";
			// $filesize = round((filesize($dir.$result) / 1024 /1024), 2);
			// if($filesize >= 1024)
			// {
			// 	$filesize = $filesize / 1024;
			// 	$filesize .= " GB";
			// }
			// else
			// {
			// 	$filesize .= " MB";
			// }
			// $modtime = date("M j, Y g:i A", filemtime($dir.$result));
			//echo "<span class='files'>".$filesize."</span>";
			//echo "<span class='files'>".$modtime."</span>";
			echo "<span class='files_actions'>";
			if($extn == "Audio")
			{
				$rndm = rand();
				echo '<audio hidden id="audio_'.$rndm.'" style="display:none;" controls="controls" class="player" preload="auto" autobuffer> <source src="'.($dir.$result).'" />
				Your browser doesn\'t support the audio element, please download to listen instead...</audio>';
				echo "<img id='image_".$rndm."' onclick='audioPlay(".$rndm.");' style='position:absolute;height:30px;left:0;margin-left:-40px;' src='../Images/play-button.png'></img>";
				echo "<div style='display:none;' id='audio_info_".$rndm."' class='audio_info'><div id='audio_progress_container_".$rndm."'class='audio_progress_container'><div id='audio_progress_".$rndm."' class='audio_progress'></div></div></div>";
			}
			if($extn != "Folder")
			{
				echo "<a href='".$dir.$result."' download><button class='pure-button-success small'>Download</button></a>
				";
			}
			echo "<button class='pure-button-error small' onclick='if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;deleteFile(this, &quot;".urlencode($pdir.$result)."&quot;, &quot;".$pdir."&quot;);'>Delete</ubtton>";
			echo "<a href='sharefile?filepath=".urlencode($dir.$result)."&filename=".$result."'><button class='pure-button-primary small'>Share</button></a>";
			echo "</span></div>";
		}
	}
	if(isset($nmr) && $nmr <= 2)
	{
		$result = str_replace(".", "", $result);
		$pdir = substr($pdir, 0, -1);
		echo "<div class='files' path='".$pdir."' onclick='if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;'>No Files in this Directory</div>";
	}
	echo "</div>";
}
?>