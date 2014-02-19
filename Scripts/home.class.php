<?php

include_once('database.class.php');
include_once('system.class.php');
include_once('user.class.php');
include_once('files.class.php');

class Home {
    
    private static $home = NULL;
    protected $system;
    protected $user;
    protected $files;
    protected $database_connection;

    public function __construct() {
        $this->user = User::getInstance();
        $this->system = System::getInstance();
        $this->files = Files::getInstance();
        $this->database_connection = Database::getConnection();
    }
    public static function getInstance ( ) {
        if (self :: $home) {
            return self :: $home;
        }

        self :: $home = new Home();
        return self :: $home;
    }
    
    function printFileList($type = NULL) {
        $files = $this->files->getList_r();
        echo "<table style='width:100%;'>";
        foreach ($files as $file) {
            if($type == $file['type'] || $type === NULL || !isset($type) || $type == "") {
                $this->fileList($file);
            }
        }
        echo "</table>";
    }
    
    function fileList($file) {
        echo "<tr><td>";
        echo "<div class='file_item' id='home_file_list_item_" . $file['id'] . "' file_id='" . $file['id'] 
                . "' style='padding:0px;border:0px;margin:0px;top:0;min-height:40px;' class='file_search_option search_option ";
        if ($file['type'] == "Folder") {
            echo "file' ";
            echo "data-file='{\"type\": &apos;" . $file['type'] . "&apos;, object={path:&apos;" . urlencode($this->system->encrypt($file['folder_id'])) . "&apos;, name:&apos;" . $file['name'] . "&apos;, description: &apos;".$file['description']."&apos;, file_id:".$file['id']."}}";
        }
        else if ($file['type'] == "Folder") {
            echo "file' data-file='{\"type\": &apos;Folder&apos;, object={path:&apos;"
            . $file['thumb_path'] . "&apos;, name:&apos;" . $file['name']
            . "&apos;, file_id:" . $file['id'] . "}}";
        }
        else if ($file['type'] == "Video") {
            $info = array(
                "thumbnail" => $file['thumbnail'],
                "flv_path" => $file['flv_path'],
                "webm_path" => $file['webm_path'],
                "ogg_path" => $file['ogg_path'],
                "mp4_path" => $file['mp4_path'],
            );
            $object = array(
                "path" => $file['path'],
                "info" => $info,
                "name" => $file['name'],
                "file_id" => $file['id'],
            );

            echo "file' data-file='{\"type\": &apos;".$file['type']."&apos;, object = (" . json_encode($object) . ");'";
        }
        else {
            $info = array(
                "thumbnail" => $file['id'],
                "flv_path" => $file['id'],
                "webm_path" => $file['id'],
                "ogg_path" => $file['id'],
                "mp4_path" => $file['id'],
            );
            $object = array(
                "path" => $file['path'],
                "info" => $info,
                "name" => $file['name'],
                "description" => $file['description'],
                "file_id" => $file['id'],
            );
            echo "file' data-file='{\"type\": \"" . $file['type'] . "\", \"object\":" . json_encode($object) . "}'";
        }
        echo ">";
        echo $this->files->filePreview($file, 'icon');
        echo "<span class='search_option_name'>" . $this->system->trimStr($file['name'], 15) . "</span>";
        //echo "<span class='search_option_info'> - ".$file['type']."</span>";
        echo "</div>";
        echo "</td></tr>";
    }

    public function getActivity($activity_query, $min_activity_id = 0) {
        $activities = $activity_query->fetchAll(PDO::FETCH_ASSOC);
        $count = count($activities);
        $max = $min_activity_id;
        foreach ($activities as $activity) {
            $this->homeify($activity);
            if ($activity['id'] > $max) {
                $max = $activity['id'];
            }
        }
        return $max;
    }
    
    function getSingleActivity($activity_id) {
        $sql = "SELECT * FROM activity WHERE id = :activity_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":activity_id" => $activity_id
        ));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function getLikes($activity) {
        $return = '';
        $who_liked_query = "SELECT user_id FROM `activity_vote` WHERE post_id = :activity_id AND vote_value = 1;";
        $who_liked_query = $this->database_connection->prepare($who_liked_query);
        $who_liked_query->execute(array(":activity_id" => $activity['id']));
        $who_liked_all = $who_liked_query->fetchAll(PDO::FETCH_ASSOC);
        $like_count = count($who_liked_all);
        $return .=  '<div class="who_liked_hover" activity_id="' . $activity['id'] . '" '
        . 'style="display:inline;"> '
        . '<span class="post_comment_time" id=' . $activity['id'] . 'likes>' . $like_count . ' likes -</span>'
        . '<div style="display:inline;" onclick="submitlike('
        . $activity["id"] . ', ' . $activity['user_id'] . ' ,1);">'
        . '<span has_liked="'
        . ($this->hasLikedPost($activity['id']) === true ? "true" : "false" )
        . '" class="user_preview_name post_comment_time home_like_icon">'
        . ($this->hasLikedPost($activity['id']) === true ? BASE::COMMENT_UNLIKE_TEXT : Base::COMMENT_LIKE_TEXT ) . '</span></div>';

        $return .= "</span></div>";

        $return .= '<div class="who_liked" id="who_liked_' . $activity['id'] . '">';

        $iteration = 0;
        foreach ($who_liked_all as $who_liked) {
            $iteration++;
            $name = $this->user->getName($who_liked['user_id']);
            if ($iteration == 1) {
                $return .=  $name;
            }
            else {
                $return .=  ",<br>" . $name;
            }
        }

        if ($like_count == 0) {
            $return .=  "No one has liked this post yet.";
        }

        $return .=  "</div>";
        return $return;
    }
    
    function getStats($activity, $align = NULL) {
        $return = '';
        $style = '';
        if($align == NULL) {
            $style .= "float:right;";
        }
        $return .= "<div class='' style='" . $style . "'>";
        $return .=  "<span class='post_comment_time' id='post_time_" . $activity['id'] . "'> "
        . $this->system->humanTiming($activity['time']) . " -</span>";
        if($activity['type'] == "File" && isset($_GET['f'])) {
            //$return .=  "<span class='post_comment_time'>" . $this->files->getViewCount($_GET['f']) . " views -</span>";
        }
        
        $return .= $this->getLikes($activity);
                if ($activity['user_id'] == $this->user->getId()) { //DELETE OPTION FOR POSTER
            $return .= "<div class='default_dropdown_actions' style='display:inline-block;' wrapper_id='activity_options_" . $activity['id'] . "'>"
                    . "<span class='default_dropdown_preview'>|</span>"
                    . "<div class='default_dropdown_wrapper' id='activity_options_" . $activity['id'] . "'>"
                    . "<ul class='default_dropdown_menu'>"
                    . "<li class='default_dropdown_item delete_activity' controller_id='activity_options_" 
                    . $activity['id'] . "'>Delete"
                    . "</li>"
                    . "<li class='default_dropdown_item edit_activity'>Edit</li>"
                    . "</ul>"
                    . "</div>"
                    . "</div>";
        }
        $return .=  "</div>";
        return $return;
    }

    function homeify($activity, $view = 'home', $activity_id = NULL) {
        $post_number = 0;
        
        if ($view == 'home') {
            echo "<div class='post_height_restrictor' id='post_height_restrictor_" . $activity['id'] . "'>";
            echo '<div activity_id="'.$activity['id'].'" id="single_post_' . $activity['id'] . '" class="singlepostdiv">';
            echo "<div id='" . $activity['id'] . "'>";     //3
            echo "<table onmouseenter='refreshContent(" . $activity['id'] . ");' class='singleupdate'><tr>"
            . "<td class='updatepic' style='width:65px;'>";
            echo "<a class='user_name_post' href='user?id=" . urlencode(base64_encode($activity['user_id'])) . "'>";
            echo "<div class='imagewrap' style='background-image:url(\""
            . $this->user->getProfilePicture("thumb", $activity['user_id'])
            . "\");'></div></a></td><td class='update'>";
            echo "<a class='user_name_post user_preview user_preview_name' user_id='" . $activity['user_id']
            . "' href='user?id=" . urlencode(base64_encode($activity['user_id'])) . "'>";
            echo $this->user->getName($activity['user_id']) . "</a>";

            echo $this->getStats($activity);

            if ($activity['type'] == "Text" || $activity['type'] == "File") {
                echo "<hr class='post_user_name_underline'>";
                echo "<p class='post_text'>" . $activity['status_text'] . '</p>';
            }

            echo $this->getPostMedia($activity);
            
            echo "</tr><tr><td></td><td>";
            
            echo '<div id= comment_div_' . $activity['id'] . ' class="comment_box">';
            echo $this->getComments($activity['id']);
            echo $this->commentInput($activity);
            

            echo "</td></tr></table></div>";
            echo "</td></tr></table></div></div>";
        }
        else if($view == "preview") {
            $return = '';
            $return .= "<table class='singleupdate'><tr>"
            . "<td class='updatepic' style='max-width:50px;'>";
            $return .= "<a class='user_name_post' href='user?id=" . urlencode(base64_encode($activity['user_id'])) . "'>";
            $return .= "<div class='imagewrap' style='background-image:url(\""
            . $this->user->getProfilePicture("thumb", $activity['user_id'])
            . "\");'></div></a></td><td class='update'>";
            $return .= "<a class='user_name_post user_preview user_preview_name' user_id='" . $activity['user_id']
            . "' href='user?id=" . urlencode(base64_encode($activity['user_id'])) . "'>";
            $return .= $this->user->getName($activity['user_id']) . "</a>";

            $return .= $this->getStats($activity, 'none');

            $return .= "</tr><tr><td colspan='2'><hr class='post_user_name_underline'></td></tr>";
            $return .= "<tr><td colspan='2'><div class='switch_container'><div class='switch_option switch_selected' onclick='changePostFeed(\"File\")'>File<div class='switch_corner'></div></div>
                        <div class='switch_option' onclick='changePostFeed(\"Post\")'>Post<div class='switch_corner'></div></div></div></td></tr>";
            $return .= "<tr><td><p class='post_text'>" . $activity['status_text'] . '</p></td>';

            $return .= "</tr><tr><td colspan='2'>";
            
            $return .= '<div activity_id="'.$activity['id'].'" id= comment_div_' . $activity['id'] . ' class="comment_box comment_box_original">';
            $return .= $this->getComments($activity['id']);
            $return .= $this->commentInput($activity);
            $return .= "</div>";
            
            $return .= "<div activity_id='".$activity_id."' class='activity_comment_div comment_box' style='display:none;'>";
            $return .= $this->getComments($activity_id);
            $return .= $this->commentInput($activity_id);
            $return .= "</div>";
            

            $return .= "</td></tr></table></div></div>";
            $return .= "</div></td></tr></table>";
            return $return;
        }
    }

    function getPostMedia($activity) {
        $assocFiles = $this->getAssocFiles($activity['id']);
        $assocFiles_num = count($assocFiles);
        if ($assocFiles_num > 0) {
            echo "<div class='post_feed_media_wrapper'>";
            foreach ($assocFiles as $file) {
                echo $this->printFileItem($file, $activity, $assocFiles_num);
            }
            echo "</div>";
        }
    }

    function commentInput($activity) {
        if(!is_array($activity)) {
            $activity = array('id' => $activity);
        }
        $return = '';
        $return .= "<div id='comment_input_" . $activity['id'] . "' class='comment_input' "
        . "style='padding-left:2px;padding-top:2px;'><table style='width:100%;'>"
        . "<tr><td style='vertical-align:top;width:40px;'>"
        . "<div class='post_comment_profile_picture post_comment_profile_picture_user' "
        . "style='background-image:url(\"" . $this->user->getProfilePicture('chat')
        . "\");'></div></td><td cellspacing='0' style='vertical-align:top;'>";

        $return .= '<textarea data-activity_id="' . $activity['id'] . '" placeholder="Write a comment..." '
        . 'class="home_comment_input_text inputtext" id="comment_' . $activity['id']
        . '"></textarea>';
        $return .= "<div class='home_comment_input_text textarea_clone' id='comment_" . $activity['id'] . "_clone'></div></td></tr></table></div>";
        return $return;
    }

    function getComments($activity_id, $get_all = null) {
        $db_query_comments = "SELECT id, time, user_id, `comment` FROM comment"
                . " WHERE activity_id = :activity_id AND visible=1 ORDER BY time DESC";
        $db_query_comments = $this->database_connection->prepare($db_query_comments);
        $db_query_comments->execute(array(":activity_id" => $activity_id));
        $numRows = $db_query_comments->rowCount();
        if ($get_all != null) {
            if ($numRows < 5) {
                
            }
            else {
                $db_query_comments = "SELECT id,time,user_id, `text` FROM comment"
                        . " WHERE post_id = :activity_id AND user_id IN "
                        . "(SELECT id FROM user WHERE position = " . $this->user->getPosition() 
                        . " AND community_id = " . $this->user->getCommunityId() . ")ORDER BY time ASC";
                $db_query_comments = $this->database_connection->prepare($db_query_comments);
                $db_query_comments->execute(array(":activity_id" => $activity_id));
                $numRows = $db_query_comments->rowCount();
                if ($numRows < 5) {
                    
                }
                else {
                    $db_query_comments = "SELECT id, time, user_id, `text` FROM comment "
                            . "WHERE post_id = :activity_id AND user_id IN "
                            . "(SELECT id FROM user WHERE position = " . $this->user->getPosition() . " "
                            . "AND community_id = " . $this->user->getCommunityId() . ") "
                            . "ORDER BY time DESC LIMIT 5 ";
                    $db_query_comments = $this->database_connection->prepare($db_query_comments);
                    $db_query_comments->execute(array(":activity_id" => $activity_id));
                }
            }
        }
        $recordcomments = $db_query_comments->fetchAll(PDO::FETCH_ASSOC);
        $recordcomments = array_reverse($recordcomments);
        $return = '';
        foreach ($recordcomments as $comment) {
            $return .= "<div class='single_comment_container' comment_id='".$comment['id']."'>";
            $return .= "<table id='post_comment_" . $activity_id . "' style='font-size: 0.9em;'><tr><td style='vertical-align:top;' rowspan='2'>";
            $return .= "<div class='post_comment_profile_picture post_comment_profile_picture_user' style='background-image:url(\"" . $this->user->getProfilePicture('chat', $comment['user_id']) . "\");'></div></td><td style='vertical-align:top;'>";
            $return .= "<a class='userdatabase_connection' href='user?id=" . urlencode(base64_encode($comment['user_id'])) . "'>";
            $return .= "<span class='user_preview user_preview_name post_comment_user_name' user_id='" . $comment['user_id'] . "'>" . $this->user->getName($comment['user_id']) . " </span></a>";
            $return .= "";
            $return .= "<span class='post_comment_text'>" . $comment['comment'] . "</span>"
            . "</td></tr><tr><td colspan=2 style='vertical-align:bottom;' >"
            . "<span class='post_comment_time'>" . $this->system->humanTiming($comment['time']) . "</span>"
            . "<span comment_id='" . $comment['id'] . "' class='post_comment_time post_comment_liked_num'>- "
            . $this->comment_like_count($comment['id']) . " likes</span>"
            . "<span has_liked='" . ($this->has_liked_comment($comment['id']) == TRUE ? "true" : "false") . "' "
            . "comment_id='" . $comment['id'] . "' "
            . "class='user_preview_name post_comment_time post_comment_vote'>"
            . ($this->has_liked_comment($comment['id']) == FALSE ? System::COMMENT_LIKE_TEXT : System::COMMENT_UNLIKE_TEXT) . "</span>";
            $return .= "</tr></table>";
            if($comment['user_id'] == $this->user->user_id) {
                $return .= "<img height='15px'src='../Images/Icons/Icon_Pacs/typicons.2.0/png-48px/delete-outline.png' class='comment_delete'></img>";
            }

            $return .= "</div><hr class='post_comment_seperator'>";
        }
        return $return;
    }

    public function getAssocFiles($activity_id = NULL) {
        $sql = "SELECT * FROM file AS file "
                . "LEFT JOIN (SELECT file_id, URL, web_title, web_description, web_favicon FROM activity_media WHERE activity_id = :activity_id) AS act ON file.id = act.file_id "
                . "WHERE file.id IN (SELECT file_id FROM activity_media WHERE activity_id = :activity_id AND visible=1); ";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
                array(
                    ":activity_id" => $activity_id,
        ));
        $file_array = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $file_array;
    }

    public function printFileItem($file, $activity, $num_files) {
        $width = 100/$num_files;
        $width = $width."%";
        $width_class = '';
        $image_styles = '';
        switch ($num_files) {
            case 1:
                $width_class = 'post_media_full ';
                $image_styles = 'max-height:400px;max-width:400px;height:auto;width:auto;';
                break;
            case 2:
                $width_class = 'post_media_single ';
                break;
            case 3:
                $width_class = 'post_media_single ';
                break;
            default:
                $width_class = 'post_media_full ';
                break;
        }
        $post_classes = " class='post_feed_item ";
        $post_styles = " style='";
        $container = "<div";
        $post_content = "";
        $classes = "";

        if ($file['type'] == "Audio") {
            $post_classes .= "post_media_double";
//            $post_styles .= " height:auto; ";
            $post_content .= $this->showDocFile($file);
        }
        else if ($file['type'] == "Image") {
            $post_styles .= "height:150px;";
            $post_classes .= "post_media_double post_media_photo";
            $post_content .= $this->showDocFile($file);
            $post_styles .= "' onclick='initiateTheater(" . $activity['id'] . ", " . $file['id'] . ");";
        }
        else if ($file['type'] == "Video") {
            $post_classes .= "post_media_video";
            $post_content .= $this->system->videoPlayer($file['id'], $file['path'], $classes, "height:100%;", "home_feed_video_", TRUE);
        }
        else if ($file['type'] == "WORD Document" 
                || $file['type'] == "PDF Document" 
                || $file['type'] == "EXCEL Document"
                || $file['type'] == "PPT Document"
                || $file['type'] == "Folder") {
            $post_styles .= "height:auto;";
            $post_classes .= "post_media_double";
            $post_content .= $this->showDocFile($file);
        }
        else if ($file['type'] == "Folder") {
            $post_styles .= "height:auto;";
            $post_classes .= "post_media_double";
            $post_content .= $this->showDocFile($file);
        }
        else if($file['type'] == "Webpage") {
            $post_classes .= "post_media_full";
            $post_styles .= "height:auto;";
            $post_content .= "<table style='height:100%;'><tr><td rowspan='3'>" .
                    "<div class='post_media_webpage_favicon' style='background-image:url(&quot;" 
                        . $file['web_favicon'] . "&quot;);'></div></td>" .
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>" .
                    "<a class='user_preview_name' target='_blank' href='" 
                    . $file['URL'] . "'><span style='font-size:13px;'>" 
                    . $file['web_title'] . "</span></a></div></td></tr>" .
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" 
                    . $file['web_description'] . "</span></td></tr></table>";
        } else {
            $post_classes .= "post_media_full";
            $post_content .= $this->showDocFile($file);
        }
        if ($file['type'] != "Webpage") {
            $post_content .= "<a href='" . $file['path'] . "' download>"
                    . "<div style='right:15px;background-image: url(\"" . Base::DOWNLOAD_ARROW . "\");' class='delete_cross delete_cross_top'>"
                    . "</div></a>";
        }
        echo "<div file_id='" . $file['id'] . "' " . $post_classes . "' " . $post_styles . "'>" . $post_content;
        if ($this->user->user_id == $activity['user_id']) {
            echo "<div style='background-image: url(\"" . Base::DELETE . "\");' class='delete_cross delete_cross_top remove_event_post'></div>";
        }
        echo "</div>";
    }

    private function showDocFile($file) {
        $preview_classes = $preview_styles = $preview_content = '';

        $post_content = $post_content_under_title = '<tr><td>';
        
        $return = $link = $path = NULL;
        $link = "files?f=".$file['id'];
        $path = $this->files->getFileTypeImage($file, 'THUMB');
        
        if($file['type'] == "Folder") {
            $path = System::FOLDER_THUMB;
            $link = "files?pd=".urlencode($this->system->encrypt($file['folder_id']));
        } else if($file['type'] == "Image") {
            $path = $file['thumb_path'];
            $preview_classes .= "post_media_photo";
            $preview_styles .= "width:auto;height:100%;background-image: url(\"".$path."\")";
            $preview_content .= "<div class='fade_right_shadow'></div><img style='opacity:0;max-width:150px;max-height:150px;' src='".$path."'></img>"; //Shadow
            
        } else if($file['type'] == "Audio") {
            $preview_styles = 'background-image: none;';
            $preview_content .= $this->system->audioButton($file['id'], $file['thumb_path']);
            $post_content_under_title .= $this->system->audioTimeline($file['id']);
        } else {
            $preview_classes .= "";
            $preview_styles .= "background-image: url(\"".$path."\")";
            $preview_content .= "<div class='fade_right_shadow'></div><img style='opacity:0;max-width:150px;max-height:150px;' src='".$path."'></img>";
        }

        $return = "<table style='table-layout:auto;width:100%;'><tr><td style='width:10px;' rowspan='4'>"
                . "<div class='post_media_preview " . $preview_classes . "' style='background-image:url(&quot;"
                . $path . "&quot;); " . $preview_styles . "'>" . $preview_content . "</div></td>"
                . "<td style='height:10px;'><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>"
                . "<a class='user_preview_name' target='_blank' href='".$link."'>"
                . "<p class='ellipsis_overflow' style='max-width:90%;margin:0px;font-size:13px;'>"
                . $file['name'] . "</p></a></div></td></tr>"
                . $post_content_under_title
                . "</td></tr><tr><td style='height:20px;'><span style='font-size:12px;' class='post_comment_time'>"
                . $file['description'] . "</span></td></tr><tr><td><span class='post_comment_time'>Created: "
                . $this->system->date($file['time']). "</span></td></tr>"
                . $post_content. "</td></tr></table>";
//        <td rowspan='5'><hr class='post_media_separator' /></td>
        return $return;
    }

    function deletePost($post_id) {
        $school_query = "UPDATE activity SET visible = 0 WHERE id = :post_id; "; //DELETE FROM activity_share WHERE activity_id = :post_id;";
        $school_query = $this->database_connection->prepare($school_query);
        $school_query->execute(array(":post_id" => $post_id));
        echo "200";
    }

    private function hasLikedPost($post_id) {
        $query = "SELECT id FROM activity_vote WHERE user_id = :user_id AND activity_id = :post_id AND vote_value = 1;";
        $query = $this->database_connection->prepare($query);
        $query->execute(array(":user_id" => $this->user->getId(), ":post_id" => $post_id));
        $num = $query->rowCount();

        if ($num == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    private function hasVotedPost($post_id) {
        $query = "SELECT id FROM activity_vote WHERE user_id = :user_id AND activity_id = :post_id;";
        $query = $this->database_connection->prepare($query);
        $query->execute(array(":user_id" => $this->user->getId(), ":post_id" => $post_id));
        $num = $query->rowCount();

        if ($num == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public function like($activity_id, $receiver_id) {
        $has_liked = $this->hasLikedPost($activity_id);
        $has_voted = $this->hasVotedPost($activity_id);
        if ($has_voted === false) {
            $insert_query = "INSERT INTO `activity_vote` (activity_id, user_id, vote_value) VALUES( :activity_id, :user_id, 1);";
            $insert_query = $this->database_connection->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $insert_query->execute(
                    array(":activity_id" => $activity_id,
                        ":user_id" => $this->user->getId()
            ));
            $this->notifyUserLike($activity_id, $receiver_id);
        }
        else {
            if ($has_liked === false) {
                $insert_query = "UPDATE activity_vote SET vote_value = 1 WHERE activity_id = :post_id AND user_id = :user_id;";
                $insert_query = $this->database_connection->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $insert_query->execute(
                        array(":post_id" => $activity_id,
                            ":user_id" => $this->user->getId()
                ));
            }
            else {
                $query = "UPDATE activity_vote SET vote_value = 0 WHERE activity_id = :post_id AND user_id = :user_id;";
                $query = $this->database_connection->prepare($query);
                $query->execute(
                        array(":post_id" => $activity_id,
                            ":user_id" => $this->user->getId()
                ));
            }
        }
        return $this->getLikeNumber($activity_id);
    }
    
    private function notifyUserLike($activity_id, $receiver_id) {
        $this->user->notify("like", $receiver_id, $activity_id, NULL);
    }
    
    function updatePost($activity_id, $text, $files) {
        $sql = "UPDATE activity SET status_text = :text WHERE id = :id";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":text" => $text,
            ":id" => $activity_id
        ));
    }

    public function getLikeNumber($post_id) {
        $who_liked_query = "SELECT id FROM `activity_vote` WHERE vote_value = 1 AND activity_id = :activity_id;";
        $who_liked_query = $this->database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $who_liked_query->execute(array(":activity_id" => $post_id));
        $like_count = $who_liked_query->rowCount();
        return $like_count;
    }
    
    function comment_like_count($comment_id) {
        $sql = "SELECT id FROM comment_vote WHERE comment_id = :comment_id AND visible=1;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":comment_id" => $comment_id
        ));
        $num = $sql->rowCount();
        return $num;
    }
    
    function has_liked_comment($comment_id) {
        $sql = "SELECT id FROM comment_vote WHERE user_id = :user_id AND comment_id = :comment_id AND visible=1;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id,
            ":comment_id" => $comment_id
        ));
        $num = $sql->rowCount();
        if($num === 0) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    function has_voted_comment($comment_id) {
        $sql = "SELECT id FROM comment_vote WHERE user_id = :user_id AND comment_id = :comment_id";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id,
            ":comment_id" => $comment_id
        ));
        $num = $sql->rowCount();
        if($num === 0) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    function comment_like($comment_id, $post_id) {
        if ($this->has_voted_comment($comment_id) == TRUE) {
            $sql = "UPDATE notification SET visible=0 WHERE type='comment_like' AND post_id=:post_id, element_id = :element_id "
                    . "AND sender_id=:user_id;"
                    . "UPDATE comment_vote SET visible = 1 WHERE user_id= :user_id AND comment_id = :element_id;";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(array(
                ":user_id" => $this->user->user_id,
                ":post_id" => $post_id,
                ":element_id" => $comment_id
            ));
        }
        else {
            $sql = "INSERT INTO comment_vote (user_id, comment_id) VALUES (:user_id, :comment_id)";
            $user_sql = "SELECT user_id FROM comment WHERE id = :comment_id;";
            $user_sql = $this->database_connection->prepare($user_sql);
            $user_sql->execute(array(
                ":comment_id" => $comment_id
            ));
            $user_id = $user_sql->fetchColumn();
            $this->user->notify('comment_vote', $user_id, $post_id, $comment_id, 0, 0);
                    $sql = $this->database_connection->prepare($sql);
            $sql->execute(array(
                ":user_id" => $this->user->user_id,
                ":comment_id" => $comment_id
            ));
        }
    }
    function remove_comment_like($comment_id) {
        $sql = "UPDATE comment_vote SET visible=0 WHERE user_id = :user_id AND comment_id = :comment_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id,
            ":comment_id" => $comment_id
        ));
        
        $sql = "UPDATE notification SET visible=0 WHERE type='comment_like' AND post_id=:comment_id AND sender_id=:user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id,
            ":comment_id" => $comment_id
        ));
    }

    function delete_comment($comment_id) {
        $sql = "UPDATE comment SET visible = 0 WHERE id = :comment_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":comment_id" => $comment_id
            ));
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $home = Home::getInstance();
    if (isset($_POST['activity_id'])) {
        
    }
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "deletePost") {
            $home->deletePost($_POST['post_id']);
        }
        if ($_POST['action'] == 'like') {
            echo $home->like($_POST['id'], $_POST['receiver_id']);
        }
        if ($_POST['action'] == 'comment_vote') {
            if ($home->has_liked_comment($_POST['comment_id']) === true) {
                echo $home->remove_comment_like($_POST['comment_id']);
            }
            else {   
                echo $home->comment_like($_POST['comment_id'], $_POST['post_id']);
            }
            echo $home->comment_like_count($_POST['comment_id']);
        }
        else if($_POST['action'] == 'file_list') {
            die($home->printFileList($_POST['type']));
        }
        else if($_POST['action'] == "getComments") {
            if (isset($_POST['get_all'])) {
                die($home->getComments($_POST['activity_id'], $_POST['get_all']));
            }
            else {
                die($home->getComments($_POST['activity_id']));
            }
        }
        else if($_POST['action'] == "deleteComment") {
            $home->delete_comment($_POST['comment_id']);
        }
        else if($_POST['action'] == "updatePost") {
            $files = array();
            if(isset($_POST['files'])) {
                $files = $_POST['files'];
            }
            $home->updatePost($_POST['activity_id'], $_POST['text'], $files);
        }
    }
}
?>