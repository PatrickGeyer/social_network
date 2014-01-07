<?php

include_once('lock.php');
require_once ('thumbnail.php');

$tmpFilePath = $_FILES['file']['tmp_name'];
$savename = preg_replace("/[^A-Za-z0-9.]/", '_', $_FILES['file']['name']);
$savename = str_replace('/', '_', $savename);
$savename = str_replace(' ', '_', $savename);
$savepath = 'User/Files/' . $user->getId() . "/";
$base_path = 'C:/inetpub/wwwroot/social_network/';
$parent_folder = $_POST['parent_folder'];
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

if ($_FILES['file']['error'] > 0) {
    switch ($_FILES['file']['error']) {
        case 1:
            echo "File too large!";
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
            echo "Unknown Image Error." . $_FILES['file']['error'][$count];
            break;
    }
}
else {
    if ($_FILES['file']['name'] != "" || ".") {
        $return_info = array();
        $lastInsertId;
        $pure_name = time();
        $ext = $files->findexts($_FILES['file']['name']);
        $file_name = $pure_name . "." . $ext;
        $thumbnail = $savepath . $pure_name . ".jpg";

        $flv_path;
        $mp4_path;
        $ogg_path;
        $swf_path;
        $webm_path;
        
        $mp3_path;

        $thumbsavepath;
        $iconsavepath;

        if (move_uploaded_file($tmpFilePath, "../" . $savepath . $file_name)) {
            $type = $files->getType($_FILES['file']['name']);
            if ($type == "Audio") {
                $convert_path = $base_path . $savepath . $file_name;
                $iconsavepath = $mp3_path = $thumbsavepath = $savepath . $pure_name . ".mp3";
                if($ext != "mp3") {
                    $convert = $files->convert($convert_path, $base_path . $mp3_path);
                    if ($convert != $base_path . $mp3_path) {
                        echo ("Error: " . $convert);
                    }
                }
            }
            else if ($type == "Video") {
                $convert_path = $base_path . $savepath . $file_name;
                $mp4_path = $savepath . $pure_name . ".mp4";
                $ogg_path = $savepath . $pure_name . ".ogg";
                $flv_path = $savepath . $pure_name . ".flv";
                $webm_path = $savepath . $pure_name . ".webm";
                $flv_path = $savepath . $pure_name . ".flv";
                
                array_push($return_info, array(
                    "from"=>$convert_path, 
                    "to"=>$base_path . $mp4_path, 
                    "args" => " -vcodec copy -acodec copy ", 
                    "before_args" => ""));

                array_push($return_info, array(
                    "from"=>$convert_path, 
                    "to"=>$base_path . $webm_path,
                    "args" => "-b 1500k -vcodec libvpx -acodec libvorbis -aq 3 -ab 128000 -f webm -g 30 -s 640x360", 
                    "before_args" => ""
                    ));
                //$files->convert($convert_path, $base_path . $webm_path, "-b 1500k -vcodec libvpx -acodec libvorbis -aq 3 -ab 128000 -f webm -g 30 -s 640x360", "");
//                if ($convert != $base_path . $webm_path) {
//                    echo("Error: " . $convert);
//                }
//                $convert = $files->convert($convert_path, $base_path . $ogg_path, "");
//                if ($convert != $base_path . $ogg_path) {
//                    echo("Error: " . $convert);
//                }
//                $convert = $files->convert($convert_path, $base_path . $flv_path, "");
//                if ($convert != $base_path . $flv_path) {
//                    echo("Error: " . $convert);
//                }
                array_push($return_info, array(
                    "from"=>$base_path . $webm_path, 
                    "to"=>$base_path . $thumbnail,
                    "args" => " -vframes 1 ", 
                    "before_args" => "-itsoffset -2"));

            }
            else {
                $thumbsavepath = $savepath . "thumb_" . $file_name;
                $iconsavepath = $savepath . "icon_" . $file_name;
            }

            if (strcmp($type, 'Image') === 0) {
                $resizeObj = new resize('../' . $savepath . $file_name);
                $resizeObj->resizeImage(300, 300, 'auto');
                $resizeObj->saveImage("../" . $thumbsavepath);
                $resizeObj->resizeImage(50, 50, 'crop');
                $resizeObj->saveImage("../" . $iconsavepath);
            }
            $database_connection->beginTransaction();
            $sql = "INSERT INTO `files` (user_id, filepath, thumb_filepath, icon_filepath, thumbnail, flv_path, mp4_path, ogg_path, webm_path, name, type, parent_folder_id) "
                    . "VALUES (:user_id, :file_path, :thumbsavepath, :iconsavepath, :thumbnail, :flv_path, :mp4_path, :ogg_path, :webm_path, :name, :type, :parent_folder);";
            $sql = $database_connection->prepare($sql);
            $sql->execute(array(
                ":user_id" => $user->getId(),
                ":file_path" => $savepath . $file_name,
                ":thumbsavepath" => $thumbsavepath,
                ":iconsavepath" => $iconsavepath,
                ":thumbnail" => $thumbnail,
                ":name" => $_FILES['file']['name'],
                ":type" => $type,
                ":parent_folder" => $parent_folder,
                ":flv_path" => $flv_path,
                ":mp4_path" => $mp4_path,
                ":ogg_path" => $ogg_path,
                ":webm_path" => $webm_path,
            ));
            $lastInsertId = $database_connection->lastInsertId(); 
            $database_connection->commit();
        }
        else {
            echo "Upload Failed!";
        }
        die(json_encode($return_info));
        //echo json_encode(array("file_id" => $lastInsertId, "filename" => $_FILES['file']['name'], "filepath" => $thumbsavepath));
    }
}
?>