<?php

include_once('user.class.php');
include_once('system.class.php');

class Files{
    protected $user;
    private $database_connection;
    public  $supported_types = array(
        "mp3", "jpg",
    );
    public $video_types = array(
        "mp4", "webm", "ogg",
    );
    private $system;
    public function __construct() {
        $this->user = new User();
        $this->database_connection = Database::getConnection();
    }

    function getList_r($id = null, $dir = null) {
        $sql = "SELECT * FROM files WHERE user_id = :user_id AND parent_folder_id != 0 ORDER BY name;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user->user_id));
        $return_array = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $return_array;
    }

    public function getSharedList($viewed_id = NULL, $id = null, $parent_folder = 0) {
        if ($id == null) {
            $id = $this->user->user_id;
        }
        $sql = "SELECT * FROM files WHERE id IN (SELECT file_id FROM file_share WHERE user_id = :viewed_id "
                . "AND (receiver_id = :user_id OR (position = :user_position AND community_id = :user_school)"
                . "OR group_id IN(SELECT group_id FROM group_member WHERE member_id = :user_id)));";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id, 
            ":user_position" => $this->user->getPosition(), 
            ":user_school" => $this->user->getCommunityId(), 
            ":viewed_id" => $viewed_id,
            ));
        $return_array = $sql->fetchAll();

        $sql = "SELECT * FROM files WHERE id IN "
                . "(SELECT file_id FROM activity_media WHERE activity_id IN "
                . "(SELECT activity_id FROM activity_share WHERE "
                . "receiver_id = :user_id OR (year = :user_position AND community_id = :user_community)"
                . "OR group_id IN(SELECT group_id FROM group_member WHERE member_id = :user_id)));";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id,
            ":user_position" => $this->user->getPosition(),
            ":user_community" => $this->user->getCommunityId(),
            ":viewed_id" => $viewed_id,
        ));
        $return_array = array_merge($return_array, $sql->fetchAll());
        return $return_array;
    }
    
    public function styleRecentlyShared($file) {
        $this->system = new System();
        $file_type = $this->getType($file['filepath']);
        $post_classes   =   " class='files_feed_item ";
        $post_styles    =   " style='";
        $container      =   "<div";
        $post_content   =   "";

        if($file_type == "Audio") {
            $post_classes   .=  "files_shared_audio";
            $post_styles    .=  " height:auto; ";
            $post_content   .=  $this->system->audioPlayer($file['thumb_filepath'], $file['name'], false, false);
        } else if($file_type == "Image") {
            $post_styles    .=  "background-image:url(\"".$file['thumb_filepath']."\")' onclick='initiateTheater(&quot;".$file['filepath']."&quot;, ".$activity['id'].");";
        } else if($file_type == "Video") {
            $post_classes   .=  "files_shared_video";
            $post_content   .=  $this->system->videoPlayer($file['id'], $file['filepath'], $classes, "height:100%;", "home_feed_video_", TRUE);
        } else {
            $post_classes   .=  "post_media_full";
            $post_styles    .=  "height:auto;";
            $post_content   .=  "<table style='height:100%;'><tr><td rowspan='3'>".
                    "<div class='post_media_webpage_favicon' style='background-image:url(&quot;" . $file['web_favicon'] . "&quot;);'></div></td>".
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>".
                    "<a class='user_preview_name' target='_blank' href='" . $file['URL'] . "'><span style='font-size:13px;'>" . $file['web_title'] ."</span></a></div></td></tr>".
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" . $file['web_description'] . "</span></td></tr></table>";
        }
        return "<div ".$post_classes."' ".$post_styles."'>".$post_content."</div>";
    }
    public function findexts($filename) {
        $filename = strtolower($filename);
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public function stripexts($filename) {
        $exts = preg_replace("/\\.[^.\\s]{3,4}$/", "", $filename);
        return $exts;
    }
    public function convert($from, $to, $args = NULL, $before_args = NULL) {
        $report = "";//" -report ";
        $progress = $to.'.txt';
        
            chdir('C:/inetpub/wwwroot/Global_Tools/ffmpeg/bin/');
            $cmd = 'ffmpeg '.$report.$before_args.' -i "'.$from.'" '.$args.' "'.$to.'" 1> '.$progress.' 2>&1';
            $result = shell_exec($cmd);
            
        return $to;
    }
    public function getConversionProgress($file) {
        $content = @file_get_contents($file.".txt");
        if($content){
        //get duration of source
        preg_match("/Duration: (.*?), start:/", $content, $matches);

        $rawDuration = $matches[1];

        //rawDuration is in 00:00:00.00 format. This converts it to seconds.
        $ar = array_reverse(explode(":", $rawDuration));
        $duration = floatval($ar[0]);
        if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
        if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;

        //get the time in the file that is already encoded
        preg_match_all("/time=(.*?) bitrate/", $content, $matches);

        $rawTime = array_pop($matches);

        //this is needed if there is more than one match
        if (is_array($rawTime)){$rawTime = array_pop($rawTime);}

        //rawTime is in 00:00:00.00 format. This converts it to seconds.
        $ar = array_reverse(explode(":", $rawTime));
        $time = floatval($ar[0]);
        if (!empty($ar[1])) $time += intval($ar[1]) * 60;
        if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;

        //calculate the progress
        $progress = round(($time/$duration) * 100);

        echo "Duration: " . $duration . "<br>";
        echo "Current Time: " . $time . "<br>";
        echo "Progress: " . $progress . "%";

    }
    }
    function getType($extn) {
        $extn = $this->findexts($extn);
        switch ($extn) {
            
            case "png" :
            case "jpg" :
            case "jpeg" :
            case "svg" :
            case "gif" :
            case "ico" :
                $extn = "Image";
                break;

            case "mp4" :
            case "mov" :
            case "wmv" :
            case "avi" :
            case "mpg" :
            case "mpeg" :
            case "m4p" :
            case "mkv" :
                $extn = "Video";
                break;

            case "mp3" :
            case "wav" :
            case "m4a" :
                $extn = "Audio";
                break;

            case "txt" :
                $extn = "Text File";
                break;
            case "pdf" :
                $extn = "PDF Document";
                break;
            case "docx" :
                $extn = "WORD Doc";
                break;
            case "zip" :
                $extn = "ZIP Archive";
                break;
            case "bak" :
                $extn = "Backup File";
                break;

            case "" :
                $extn = "Folder";
                break;

            default :
                $extn = strtoupper($extn) . " File";
                break;
        }
        return $extn;
    }

    function getContents($parent_folder = 1, $user_id = null) {
        if ($user_id == null) {
            $user_id = $this->user->user_id;
        }
        $sql = "SELECT * FROM files WHERE user_id = :user_id AND parent_folder_id = :parent_folder ORDER BY name;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $user_id, ":parent_folder" => $parent_folder));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    function tableSort($file, $actions = true, $directions = false, $viewed_id = null) {
        if ($file['name'] != "" && $file['name'] != "." && $file['name'] != "..") {
            $extn = $this->getType($file['name']);



            if ($extn == "Folder") {
                echo $this->folderPreview($file['id'], $file['folder_id'], $file['filepath']);
            }
            else {
                echo "<div id='file_div_" . $file['id'] . "' path='" . $file['name'] . "' class='files'>";
                if ($extn == "Audio") {
                    echo $this->audioPreview($file['id'], $file['name'], $file['thumb_filepath']);
                }
                else if ($extn == "Image") {
                    echo $this->imagePreview($file['id'], $file['thumb_filepath']);
                }
                else if ($extn == "Video") {
                    echo $this->videoPreview($file);
                }
            }

            if ($directions == true) {
                $this->printDirections($file, $viewed_id);
            }
            else {
                echo "<p class='files ellipsis_overflow'>" . $this->stripexts($file['name'])."</p>";
                if($extn == "Video" || $extn == "Audio") {
                    echo "<div id='audio_play_icon_" . $file['id'] . "' "
                . "class='file_play_icon'"
                . ">&#9658;</div>";
                }
            }
            echo "<div class='files_actions'><table><tr style='vertical-align:middle;'><td>";

            if ($extn != "Folder") {
                echo "<a href='" . $file['filepath'] . "' download><div class='files_actions_item files_actions_download'></div></a></td><td>";
                echo "<hr class='files_actions_seperator'></td><td>";
            }

            if ($actions != false) {
                echo "<div class='files_actions_item files_actions_delete' "
                . "onclick='deleteFile(this, " . $file['id'] . ");if(event.stopPropagation){event.stopPropagation();}"
                . "event.cancelBubble=true;'></div></td><td>"
                . "<hr class='files_actions_seperator'></td><td>"
                . "<a href='sharefile?file_id=" . urlencode($file['id']) . "&filename=" . $file['name'] . "'>"
                . "<div class='files_actions_item files_actions_share' ></div></a></td>";
            }

            if ($extn == "Audio" || $extn == "Video") {
                echo "<td><hr id='audio_play_icon_seperator_" . $file['id'] . "' style='display:none;' "
                . "class='files_actions_seperator'></td><td><div id='audio_play_icon_" . $file['id'] . "' "
                . "class='audio_play_icon' style='display:none;'"
                . ">&#9658;</div></td>";
            }

            echo "</tr></table></div>";
            echo "</div>";
        }
    }
    
    private function printDirections($file, $viewed_id) {
        $sql        = "SELECT * FROM file_share WHERE file_id = :file_id;";
        $sql        = $this->database_connection->prepare($sql);
        $sql->execute(array(":file_id" => $file['id']));
        $file_props = $sql->fetch(PDO::FETCH_ASSOC);
           
        $options    = array();
        
        $school_sql = "SELECT name FROM community WHERE id = :receiver_id;";
        $group_sql  = "SELECT group_name FROM `group` WHERE id = :receiver_id;";
        
        $final_sql;

        switch ($file_props) {
            case isset($file_props['year']):
            case isset($file_props['community_id']):
                $final_sql  = $school_sql;
                $options    = array(
                    ":receiver_id" => $file_props['community_id']
                        );
                break;

            case isset($file_props['group_id']):
                $final_sql  = $group_sql;
                $options    = array(
                    ":receiver_id" => $file_props['group_id']
                        );
                break;

            case isset($file_props['receiver_id']):
                echo $receiver = $this->user->getName($file_props['receiver_id']);
                break;
        }
        $sql = $this->database_connection->prepare($final_sql);
        $sql->execute($options);
        $receiver = $sql->fetchColumn();
        
        if(!empty($final_sql)) {
            echo $receiver;
        }
        echo "<p class='files ellipsis_overflow'>" . $this->user->getName($viewed_id) . " &#65515; &#10162; <em>" . $this->stripexts($file['name']) . "</em></p><br>";
    }

    private function imagePreview($file_id, $thumbpath) {
        return "<div class='audio_hidden_container' id='file_div_hidden_container_" . $file_id . "'>"
                . "<div id='buffer_" . $file_id . "' style='position:absolute;height:20px;width:20px;"
                . "background-image:url(\"Images/ajax-loader.gif\");background-size:cover;'></div>"
                . "<div style='background-image:url(&quot;" . $thumbpath . "&quot;);"
                . "background-size:contain;background-repeat:no-repeat;background-position:center;width:100%;height:100%;max-height:400px;'>"
                . "<img class='image_placeholder' f_id='" . $file_id . "'"
                . "style=' visibility:hidden; width:100%;height:100%;max-width:300px;max-height:280px;' src='" . $thumbpath
                . "'onload='$(\"#buffer_" . $file_id . "\").fadeOut();resizeDiv($(this));'></img>"
                . "</div></div>";
    }
    
    private function audioPreview($file_id, $name, $path) {
        //$this->system = new System();
         return "<div class='audio_hidden_container' id='file_div_hidden_container_" . $file_id . "'>"
                . $this->system->audioPlayer($path, $this->stripexts($name), false, $file_id)
                . "</div>";
    }
    
    private function videoPreview($file) {
        $this->system = new System();
        return "<div class='audio_hidden_container' style='height:200px;' id='file_div_hidden_container_" . $file['id'] . "'>"
                . $this->system->videoPlayer($file['id'], $file['filepath'], "file_video", NULL, "file_video_", TRUE)
                . "</div>";
    }
    
    private function folderPreview($file_id, $folder_id, $path) {
        return "<div id='file_div_" . $file_id . "' onclick ='window.location.assign(&quot;files?pd="
                . urlencode($this->system->encrypt($folder_id))
                . "&quot;);' id='" . $path . "' class='folder'>";
    }
    
    
    
    function createFolder($parent_folder = 1, $name) {
        $sql = "SELECT MAX(folder_id) FROM files WHERE user_id = :user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user->user_id));
        $new_folder_id = $sql->fetchColumn();
        if ($new_folder_id == "") {
            $new_folder_id = 1;
        } else {
            $new_folder_id++;
        }

        $sql = "INSERT INTO files(user_id, name, folder_id,parent_folder_id) VALUES (:user_id, :name, :folder_id, :parent_folder_id);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user->user_id, ":name" => $name, ":folder_id" => $new_folder_id, ":parent_folder_id" => $parent_folder));
        return true;
    }

    function getParentId($folder_id) {
        $sql = "SELECT parent_folder_id FROM files WHERE user_id = :user_id AND folder_id = :folder_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user->user_id, ":folder_id" => $folder_id));
        return $sql->fetchColumn();
    }

    function delete($id) {
        $sql = "SELECT filepath, thumb_filepath, icon_filepath,flv_path,mp4_path,webm_path,ogg_path, thumbnail, type FROM `files` WHERE user_id = :user_id AND id = :id";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user->user_id, ":id" => $id));
        $filepath = $sql->fetch();
        //if($type != "Folder") {
            $sql = "DELETE FROM files WHERE user_id = :user_id AND id = :id;";
            foreach ($filepath as $key => $value) {
                unlink("../" . $value);
            }
        //} else {
            //$sql = "DELETE FROM files WHERE user_id = :user_id AND parent_folder_id = :id";
        //}

        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $this->user->user_id, ":id" => $id));
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
            die($files->delete($_POST['id']));
        } else if ($_POST['action'] == "createFolder") {
            $files->createFolder($_POST['parent_folder'], $_POST['folder_name']);
        }
        if($_POST['action'] == "convert") {
            foreach ($_POST['file_info'] as $file) {
                $files->convert($file['from'], $file['to'], $file['args'], $file['before_args']);
            }
        }
        if($_POST['action'] == "get_conversion_progress") {
            foreach ($_POST['file_info'] as $file) {
                $files->getConversionProgress($file['to']);
            }
        }
    }
}