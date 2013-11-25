<?php
include_once('lock.php');

$tmpFilePath = $_FILES['file']['tmp_name'];
$savename = preg_replace("/[^A-Za-z0-9.]/", '_', $_FILES['file']['name']);
$savename = str_replace('/', '_', $savename);
$savename = str_replace(' ', '_', $savename);
$savepath = $_POST['dir'];
$dir = $_SERVER['DOCUMENT_ROOT'].'/'.$_POST['dir'];
if (!file_exists($dir)) 
{
	mkdir ($dir, 0777, true);
}

if($_FILES['file']['error'] > 0)
{
	switch($_FILES['file']['error'])
	{
		case 1:
		echo "Image too large!";
		break;

		case 2:
		echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
		break;

		case 3:
		echo "The uploaded file was only partially uploaded.";
		break;

		case 4:
					//echo "No file was uploaded.";
		break;

		case 6:
		echo "Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.";
		break;

		case 7:
		echo "Failed to write file to disk. Introduced in PHP 5.1.0.";
		break;

		case 8:
		echo "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.";
		break;

		default:
		echo "Unknown Image Error.".$_FILES['file']['error'][$count];
		break;
	}
}
else 
{
	if($_FILES['file']['name'] != "")
	{
		if(move_uploaded_file($tmpFilePath, "../".$savepath.$_FILES['file']['name']))
		{
			echo "success/".$savepath;
		}
		else
		{
			echo "Upload Failed!";
		}
	}
}
?>