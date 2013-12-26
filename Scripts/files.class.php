<?php

include_once('user.class.php');
include_once('system.class.php');

class Files extends User {

    private $system;

    public function __construct() {
        parent::__construct();
        $this->system = new System;
    }

//    function getList($id = null, $dir = null) {
//        if ($id == null) {
//            $id = $this->user_id;
//        }
//        if ($dir == null) {
//            $dir = "User/Files/" . $this->user_id . "/";
//        }
//        $nmr = 0;
//        $return_array = array();
//        $scan = scandir($dir);
//        foreach ($scan as $result) {
//            if ($result != "" && $result != "." && $result != "..") {
//                $nmr += 1;
//                $extn = $this->getType($result);
//
//                $return_array[$nmr]['type'] = $extn;
//                $return_array[$nmr]['name'] = $this->stripexts($result);
//                $return_array[$nmr]['path'] = $dir . $result;
//
//                $filesize = round((filesize($dir . $result) / 1024 / 1024), 2);
//                if ($filesize >= 1024) {
//                    $filesize = $filesize / 1024;
//                    $filesize .= " GB";
//                } else {
//                    $filesize .= " MB";
//                }
//                $return_array[$nmr]['size'] = $filesize;
//            }
//        }
//        return $return_array;
//    }

    function getList_r($id = null, $dir = null) {
        $sql = "SELECT * FROM files WHERE user_id = :user_id AND parent_folder_id != 0 ORDER BY name;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id));
        $return_array = array();
        foreach ($sql->fetchAll() as $key => $result) {
            if ($result != "" && $result != "." && $result != "..") {
                $extn = $this->getType($result['name']);
                $return_array[$key]['type'] = $extn;
                $return_array[$key]['name'] = $this->stripexts($result['name']);
                $return_array[$key]['filepath'] = $result['filepath'];
                $return_array[$key]['thumb_filepath'] = $result['thumb_filepath'];
                $return_array[$key]['icon_filepath'] = $result['icon_filepath'];
                $return_array[$key]['folder_id'] = $result['folder_id'];
                $return_array[$key]['parent_folder_id'] = $result['parent_folder_id'];

                // $filesize = round((filesize($result['filepath']) / 1024 /1024), 2);
                // if($filesize >= 1024)
                // {
                // 	$filesize = $filesize / 1024;
                // 	$filesize .= " GB";
                // }
                // else
                // {
                // 	$filesize .= " MB";
                // }
                // $return_array[$key]['size'] = $filesize;
            }
        }
        return $return_array;
    }

    function getSharedList($viewed_id, $id = null, $parent_folder = 0) {
        if ($id == null) {
            $id = $this->user_id;
        }
        $sql = "SELECT * FROM files WHERE id IN (SELECT file_id FROM file_share WHERE user_id = :viewed_id AND (receiver_id = :user_id OR (position = :user_position AND community_id = :user_school)
			OR group_id IN(SELECT group_id FROM group_member WHERE member_id = :user_id)));";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id, ":user_position" => $this->getPosition(), ":user_school" => $this->getCommunityId(), ":viewed_id" => $viewed_id));
        $return_array = $sql->fetchAll();
        return $return_array;
    }

    function findexts($filename) {
        $filename = strtolower($filename);
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    function stripexts($filename) {
        $exts = preg_replace("/\\.[^.\\s]{3,4}$/", "", $filename);
        return $exts;
    }

    function getType($extn) {
        $extn = $this->findexts($extn);
        switch ($extn) {
            case "png": $extn = "Image";
                break;
            case "jpg": $extn = "Image";
                break;
            case "jpeg": $extn = "Image";
                break;
            case "svg": $extn = "Image";
                break;
            case "gif": $extn = "Image";
                break;
            case "ico": $extn = "Image";
                break;

            case "txt": $extn = "Text File";
                break;
            case "log": $extn = "Log File";
                break;
            case "htm": $extn = "HTML File";
                break;
            case "php": $extn = "PHP Script";
                break;
            case "js": $extn = "Javascript";
                break;
            case "css": $extn = "Stylesheet";
                break;
            case "pdf": $extn = "PDF Document";
                break;
            case "docx":$extn = "WORD Doc";
                break;

            case "zip": $extn = "ZIP Archive";
                break;
            case "bak": $extn = "Backup File";
                break;

            case "mp3": $extn = "Audio";
                break;
            case "wav": $extn = "Audio";
                break;
            case "m4a": $extn = "Audio";
                break;

            case "": $extn = "Folder";
                break;

            default: $extn = strtoupper($extn) . " File";
                break;
        }
        return $extn;
    }

    function getContents($parent_folder = 1, $user_id = null) {
        if ($user_id == null) {
            $user_id = $this->user_id;
        }
        $sql = "SELECT * FROM files WHERE user_id = :user_id AND parent_folder_id = :parent_folder ORDER BY name;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $user_id, ":parent_folder" => $parent_folder));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    function tableSort($file, $actions = true, $directions = false, $viewed_id = null) {
        if ($file['name'] != "" && $file['name'] != "." && $file['name'] != "..") {
            $extn = $this->getType($file['name']);
            if ($extn == "Audio") {
                echo "<div class='audio_hidden_container' id='file_div_hidden_container_" . $file['id'] . "'>";
                echo $this->system->audioPlayer($file['filepath'], $this->stripexts($file['name']), false, $file['id']);
                echo "</div>";
            }
            if ($extn == "Image") {
                echo "<div class='audio_hidden_container' id='file_div_hidden_container_" . $file['id'] . "'>";
                echo "<div id='buffer_" . $file['id'] . "' style='position:absolute;height:20px;width:20px;background-image:url(\"Images/ajax-loader.gif\");background-size:cover;'></div>";
                echo "<div style='background-image:url(&quot;" . $file['thumb_filepath'] . "&quot;);background-size:contain;background-repeat:no-repeat;background-position:center;width:100%;height:100%;max-height:400px;'>";
                echo "<img class='image_placeholder' f_id='" . $file['id'] . "' style=' visibility:hidden; width:100%;height:100%;max-width:300px;max-height:280px;' src='" . $file['thumb_filepath'] . "'
				onload='$(\"#buffer_" . $file['id'] . "\").fadeOut();resizeDiv($(this));'></img>";
                echo "</div></div>";
            }
            if ($extn == "Folder") {
                echo "<div id='file_div_" . $file['id'] . "' onclick ='window.location.assign(&quot;files?pd=" . urlencode($this->system->encrypt($file['folder_id'])) . "&quot;);' id='" . ($file['filepath']) . "' class='folder'>";
            } else if ($extn == "Audio") {
                echo "<div id='file_div_" . $file['id'] . "' path='" . $file['name'] . "' class='files audio_file'>";
            } else {
                echo "<div id='file_div_" . $file['id'] . "' path='" . $file['name'] . "' class='files'>";
            }

            if ($directions == true) {
                echo "<span class='files'>" . $this->getName($viewed_id) . " &#65515; ";
                $sql = "SELECT * FROM file_share WHERE file_id = :file_id;";
                $sql = $this->database_connection->prepare($sql);
                $sql->execute(array(":file_id" => $file['id']));
                $file_props = $sql->fetch(PDO::FETCH_ASSOC);

                switch ($file_props) {
                    case isset($file_props['year']):
                        $sql = "SELECT name FROM community WHERE id = :receiver_id;";
                        $sql = $this->database_connection->prepare($sql);
                        $sql->execute(array(":receiver_id" => $file_props['community_id']));
                        $receiver = $sql->fetchColumn();
                        echo $receiver . ", Year " . $file_props['year'];
                        break;

                    case isset($file_props['community_id']):
                        $sql = "SELECT name FROM community WHERE id = :receiver_id;";
                        $sql = $this->database_connection->prepare($sql);
                        $sql->execute(array(":receiver_id" => $file_props['community_id']));
                        $receiver = $sql->fetchColumn();
                        echo $receiver;
                        break;

                    case isset($file_props['group_id']):
                        $sql = "SELECT group_name FROM `group` WHERE id = :receiver_id;";
                        $sql = $this->database_connection->prepare($sql);
                        $sql->execute(array(":receiver_id" => $file_props['group_id']));
                        $receiver = $sql->fetchColumn();
                        echo $receiver;
                        break;

                    case isset($file_props['receiver_id']):
                        $receiver = $this->getName($file_props['receiver_id']);
                        echo "You";
                        break;
                }
                echo " &#10162; <em>" . $this->stripexts($file['name']) . "</em></span><br>";
            } else {
                echo "<span class='files'>" . $this->system->trimStr($this->stripexts($file['name']), 30) . "</span>";
            }
            echo "<div class='files_actions'><table><tr style='vertical-align:middle;'><td>";

            if($extn == "Audio") {
                echo "<div id='audio_play_icon_".$file['id']."' class='files_actions_item audio_play_icon' style='display:none;background-image:url(\"Images/Icons/Icon%20Pacs/__ecqlipse_2___PNG_by_chrfb/ecqlipse%202%20-%2048%2032%2016%20system%20black/FILE%20-%20SOUND_16x16-32.png\");' ></div></td><td>";
                echo "<hr id='audio_play_icon_seperator_".$file['id']."' style='display:none;' class='files_actions_seperator'></td><td>";
            }

            if ($extn != "Folder") {
                echo "<a href='" . $file['filepath'] . "' download><div class='files_actions_item' style='background-image:url(\"Images/Icons/Icon Pacs/glyph-icons/glyph-icons/PNG/Download.png\");' ></div></a></td><td>";
                echo "<hr class='files_actions_seperator'></td><td>";
            }

            if ($actions != false) {
                echo "<div style='background-image:url(\"Images/Icons/Icon Pacs/typicons.2.0/png-48px/delete-outline.png\")' class='files_actions_item' 
				onclick='deleteFile(this, " . $file['id'] . ");if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;'></div></td><td>";
                echo "<hr class='files_actions_seperator'></td><td>";
                echo "<a href='sharefile?file_id=" . urlencode($file['id']) . "&filename=" . $file['name'] . "'>
						<div style='background-image:url(\"Images/ShareIcon.png\");' class='files_actions_item' ></div>
					  </a></td>";
            }
            echo "</tr></table></div>";
            echo "</div>";
        } else {
            echo "Wrong name";
        }
    }

    function createFolder($parent_folder = 1, $name) {
        $sql = "SELECT MAX(folder_id) FROM files WHERE user_id = :user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id));
        $new_folder_id = $sql->fetchColumn();
        if ($new_folder_id == "") {
            $new_folder_id = 1;
        } else {
            $new_folder_id++;
        }

        $sql = "INSERT INTO files(user_id, name, folder_id,parent_folder_id) VALUES (:user_id, :name, :folder_id, :parent_folder_id);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id, ":name" => $name, ":folder_id" => $new_folder_id, ":parent_folder_id" => $parent_folder));
        return true;
    }

    function getParentId($folder_id) {
        $sql = "SELECT parent_folder_id FROM files WHERE user_id = :user_id AND folder_id = :folder_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id, ":folder_id" => $folder_id));
        return $sql->fetchColumn();
    }

    function delete($id) {
        $sql = "SELECT filepath, thumb_filepath, icon_filepath FROM `files` WHERE user_id = :user_id AND id = :id";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id, ":id" => $id));
        $filepath = $sql->fetch();
        echo $filepath;
        foreach ($filepath as $key => $value) {
            if (!is_dir($value)) {
                unlink("../" . $value);
            } else {
                $this->removeDir_r("../" . $value);
            }
        }

        $sql = "DELETE FROM files WHERE user_id = :user_id AND id = :id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user_id, ":id" => $id));
        return "200";
    }

    private function removeDir_r($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $files = new Files;
    if (isset($_POST['dir'])) {
        $pdir = $_POST['dir'] . "/";
    } else {
        $pdir = null;
    }
    $dir = "../" . $pdir;
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "getContents") {
            $files->getContents($_POST['parent_folder']);
        } else if ($_POST['action'] == "delete") {
            $files->delete($_POST['id']);
        } else if ($_POST['action'] == "createFolder") {
            $files->createFolder($_POST['parent_folder'], $_POST['folder_name']);
        }
    }
}