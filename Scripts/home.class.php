<?php

include_once('database.class.php');
include_once('extends.class.php');
include_once('system.class.php');
include_once('user.class.php');
include_once('files.class.php');

class Home extends Database {

    private $phrases;
    private $system;
    private $user;
    private $files;

    public function __construct() {
        parent::__construct();
        $phrases_query = "SELECT * FROM phrases;";
        $phrases_query = $this->database_connection->prepare($phrases_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $phrases_query->execute();
        $this->phrases = $phrases_query->fetch(PDO::FETCH_ASSOC);
        $this->user = new User;
        $this->system = new System;
        $this->files = new Files;
    }

    function fileList($file) {
        echo "<tr><td>";
        echo "<div id='home_file_list_item_" . $file['id'] . "' style='padding:0px;border:0px;margin:0px;top:0;background:transparent;min-height:40px;' class='search_option ";
        if ($file['type'] == "Folder") {
            echo "folder' ";
            echo "onclick='addToStatus(&apos;" . $file['type'] . "&apos;, object={path:&apos;" . urlencode($this->system->encrypt($file['folder_id'])) . "&apos;, name:&apos;" . urlencode($this->system->encrypt($this->user->getId())) . "&apos;});'";
        } else if($file['type'] == "Audio") {
            echo "file' onclick='addToStatus(&apos;" . $file['type'] . "&apos;, object={path:&apos;" 
                    . $file['thumb_filepath'] . "&apos;, name:&apos;" . $file['name'] 
                    . "&apos;, file_id:".$file['id']."});'";
        } else if($file['type'] == "Video") {
            $info = array(
                "thumbnail" => $file['thumbnail'],
                "flv_path" => $file['thumb_filepath'],
                "ogg_path" => $file['icon_filepath'],
                "mp4_path" => $file['filepath'],
            );
            $object = array(
                "info" => $info,
                "name" => $file['name'],
                "file_id" => $file['id'],
            );

           echo "file' onclick='var object = (".  json_encode($object).");addToStatus(&apos;" . $file['type'] . "&apos;, object);'";
        } else {
            echo "file' onclick='addToStatus(&apos;" . $file['type'] . "&apos;, object={path:&apos;" 
                    . $file['thumb_filepath'] . "&apos;, name:&apos;" . $file['name'] 
                    . "&apos;, file_id:".$file['id']."});'";
        }
        echo ">";
        if ($file['type'] == "Image") {
            echo "<div class='search_picture' style='background-image:url(&quot;" . $file['icon_filepath'] . "&quot;);'></div>";
        } else if ($file['type'] == "Folder") {
            echo "<div class='search_picture' style='background-image:url(&quot;Images/yellow-folder-icon.jpg&quot);'></div>";
        } else if ($file['type'] == "Audio") {
            echo "<div class='search_picture' style='background-image:url(&quot;Images/Icons/music-icon.png&quot);'></div>";
        } else if($file['type'] == "Video") {
            echo "<div class='search_picture' style='background-image:url(&quot;" . $file['thumbnail'] . "&quot;);'></div>";
        }
        echo "<span class='search_option_name'>" . $this->system->trimStr($file['name'], 15) . "</span>";
        //echo "<span class='search_option_info'> - ".$file['type']."</span>";
        echo "</div>";
        echo "</td></tr>";
    }

    function homeify($activity, $database_connection, $user) {
        $post_number = 0;
        $activity_time = strtotime($activity['time']);

        echo "<div class='post_height_restrictor' id='post_height_restrictor_" . $activity['id'] . "'>";
        echo '<div id="single_post_' . $activity['id'] . '" class="singlepostdiv">';
        echo "<div id='" . $activity['id'] . "'>";
        echo "<table onmouseenter='refreshContent(" . $activity['id'] . ");' class='singleupdate'>
		<tr>
		<td class='updatepic'>";
        echo "<a class='user_name_post' href='user?id=" . urlencode(base64_encode($activity['user_id'])) . "'>";
        echo "<div class='imagewrap' style='background-image:url(\"" . $this->user->getProfilePicture("thumb", $activity['user_id']) . "\");'></div>
		</a>
		</td>
		<td class='update'>";
        echo "<a class='user_name_post user_preview user_preview_name' user_id='".$activity['user_id']."' href='user?id=" . urlencode(base64_encode($activity['user_id'])) . "'>";
        echo $this->user->getName($activity['user_id']);
        echo " </a>";

        if ($activity['type'] == "text") {
            echo "<hr class='post_user_name_underline'>";
            $activity['status_text'] = str_replace("<img", "<img onclick='initiateTheater($(this).attr(&quot;real_src&quot;)," . $activity['id'] . ");' ", $activity['status_text']);
            echo "<span class='post_text'>" . $activity['status_text'] . '</span>';
        } else if ($activity['type'] == "profile") {
            if ($user['default_language'] == "") {
                $phrase_language = "en";
            } else {
                $phrase_language = strtolower($user['default_language']);
            }
            $phrase_string = strtolower($activity['user_gender']) . "_" . $phrase_language;
            $phrase_string = "profile_picture_" . $phrase_string;
            echo $phrases[$phrase_string];
        } else if ($activity['type'] == "video") {
            $phrase_string = strtolower($activity['user_gender']) . "_" . $user['default_language'];
            echo $phrases['profile_picture_' . $phrase_string];
            echo "<embed src='" . $activity . "'></embed>";
        } else if ($activity['type'] == "file") {
            if ($user->getLanguage() == "") {
                $phrase_string = "en";
            } else {
                $phrase_string = strtolower($user->getLanguage());
            }
            $phrase_query = "file_share_" . $phrase_string;
            echo $this->phrases[$phrase_query] . "<br/><br/>";
            echo "<span style='font-style: italic;'>" . $activity['description'] . "</span>";
            echo "<br><a style='text-decoration:none; color:grey;' href='" . $activity['status_text'] . "'>File</a>";
        } else if ($activity['type'] == "folder") {
            $phrase_string = strtolower($user['default_language']);
            $phrase_string = $this->phrases['folder_share_' . $phrase_string];
            $phrase_string = str_replace('$folder', "<a href='" . $activity['status_text'] . "'>" . $activity['activity_name'] . "'</a>", $phrase_string);
            echo $phrase_string;
        } else if ($activity['type'] == "abdicate") {
            $phrase_string = strtolower($user['default_language']);
            $phrase_string = $this->phrases['abdicate_' . $phrase_string];
            $phrase_string = str_replace('$group', "<a href='" . $activity['status_text'] . "'>" . $activity['activity_name'] . "'</a>", $phrase_string);
            echo $phrase_string;
        }
        
        $assocFiles = $this->getAssocFiles($activity['id']);
        $assocFiles_num = count($assocFiles);
        if($assocFiles_num > 0) {
            echo "<div class='post_feed_media_wrapper'>";
            foreach($assocFiles as $file) {
                echo $this->printFileItem($this->files->getType($file['name']), $file, $activity);
            }
            echo "</div>";
        }

        $who_liked_query = "SELECT * FROM `votes` WHERE post_id = :activity_id AND vote_value = 1;";
        $who_liked_query = $database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $who_liked_query->execute(array(":activity_id" => $activity['id']));
        $like_count = $who_liked_query->rowCount();
        echo '</td>
		<td style="vertical-align:top; left:0;min-width:100px;">
		<span class="who_liked_hover" activity_id="' . $activity['id'] . '" style="text-decoration:none;" onclick="submitlike(' . $activity["id"] . ', ' . $activity['user_id'] . ' ,1);">
		<img style="' . ($this->hasLikedPost($activity['id']) === true ? "opacity:1;" : "opacity:0.3;") . '" class="home_like_icon" src="Images/Icons/Icon_Pacs/Batch-master/Batch-master/PNG/16x16/arrow-up.png"></img></span>
		<span id=' . $activity['id'] . 'likes>' . $like_count . '</span>
		<div class="who_liked" id="who_liked_' . $activity['id'] . '">
		';
        $liked_number = $who_liked_query->rowCount();
        $iteration = 0;
        $who_liked_all = $who_liked_query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($who_liked_all as $who_liked) {
            $iteration++;
            $who_liked_query = "SELECT name FROM `users` WHERE id = :user_id;";
            $who_liked_query = $database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $who_liked_query->execute(array(":user_id" => $who_liked['user_id']));
            $who_liked = $who_liked_query->fetch(PDO::FETCH_ASSOC);
            if ($iteration == 1) {
                echo $who_liked['name'];
            } else {
                echo ",<br>" . $who_liked['name'];
            }
        }

        if ($liked_number == 0) {
            echo "No one has liked this post yet.";
        }

        echo "</div></span></td>";
        //<td style="vertical-align:top;min-width:50px;">
        //<a href="#!" onclick="submitlike('.$activity["id"].', 0); return false;">
        //<img class="icon" src="Images/Icons/icons/thumb.png">
        //</a>
        //</td>
        //<td style="vertical-align:top;min-width:50px;">
        //	<span title="Post Popularity" class="vote_percentage" id='.$activity['id'].'vote_percentage>'.$vote_percentage.'%</span>
        //	</td>
        echo "</tr><tr><td></td><td>";
        $db_query_comments = "SELECT comment_id FROM comments WHERE post_id = :activity_id ORDER BY time ASC";
        $db_query_comments = $this->database_connection->prepare($db_query_comments, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $db_query_comments->execute(array(":activity_id" => $activity['id']));
        $num = $db_query_comments->rowCount();
        echo "<div class='comments' id = 'commentcomplete_" . $activity['id'] . "'>";
        if ($num >= 0) {
            echo '<div id= comment_div_' . $activity['id'] . ' class="comment_box">';
        } else {
            echo '<div style="border: 0px;" id= comment_div_' . $activity['id'] . ' class="chatinput">';
        }

        //echo "<span onclickclass='post_expand_comments' style='font-size:10px;color:blue;text-decoration:underline;'>expand comments</span>";
        //echo "<hr class='post_comment_seperator'>";

        $this->getComments($activity['id'], $database_connection);

        echo "<div id='comment_input_" . $activity['id'] . "' class='comment_input' style='padding-left:2px;padding-top:2px;'><table style='width:100%;'><tr><td style='vertical-align:top;width:40px;'>
		<div class='post_comment_profile_picture post_comment_profile_picture_user' style='background-image:url(\"" . $this->user->getProfilePicture('chat') . "\");'></div></td><td cellspacing='0' style='vertical-align:top;'>";

        echo '<div data-placeholder="Write a comment..." contenteditable class="inputtext" id="comment_' . $activity['id'] . '" name="text" onkeydown="if (event.keyCode == 13) 
		{ submitcomment($(this).html(), ' . $activity['id'] . '); return false; }"></div>';

        echo "</td><td style='vertical-align:top;width:60px;'><button
		onclick='submitcomment($(&quot;#comment_" . $activity['id'] . "&quot;).html(), " . $activity['id'] . ");'
		style='margin-left:10px;margin-top:1px;' class='pure-button-secondary smallest inputtext_send'>send</button>";
        echo "</td></tr></table></div>";
        if ($num >= 0) {
            echo "</div>";
        } else {
            echo "</div>";
        }
        echo "</div>";
        echo "<span id='post_time_" . $activity['id'] . "' style='font-size:0.8em; color:grey; float:right;'> " . $this->system->humanTiming($activity_time) . " -</span>";
        if ($activity['user_id'] == $user->getId()) {
            echo "<span class='delete' id='delete1_post_" . $activity['id'] . "' onclick='show_Confirm(" . $activity['id'] . ");'
			style='font-family: century gothic; cursor:pointer;font-size:0.8em; color:grey; float:left;'>delete</span>";
            echo "<span class='delete' id='delete_post_" . $activity['id'] . "' onclick='delete_post(" . $activity['id'] . ");'
			style='font-family: century gothic; visibility:hidden; cursor:pointer;font-size:0.8em; color:red; float:left;'>Confirm</span>";
        }
        echo "</td></tr></table>";
        echo "</div>";
        // close single_post div
        echo "</div>";
        echo "</div>";
    }

    function getComments($activity_id, $get_all = null) {
        $db_query_comments = "SELECT time,commenter_id, comment_text FROM comments WHERE post_id = :activity_id ORDER BY time DESC";
        $db_query_comments = $this->database_connection->prepare($db_query_comments);
        $db_query_comments->execute(array(":activity_id" => $activity_id));
        $numRows = $db_query_comments->rowCount();
        if ($get_all != null) {
            if ($numRows < 5) {
                
            } else {
                $db_query_comments = "SELECT time,commenter_id, comment_text FROM comments WHERE post_id = :activity_id AND commenter_id IN
				(SELECT id FROM users WHERE position = " . $this->user->getPosition() . " AND community_id = " . $this->user->getCommunityId() . ")ORDER BY time ASC";
                $db_query_comments = $this->database_connection->prepare($db_query_comments);
                $db_query_comments->execute(array(":activity_id" => $activity_id));
                $numRows = $db_query_comments->rowCount();
                if ($numRows < 5) {
                    
                } else {
                    $db_query_comments = "SELECT time,commenter_id, comment_text FROM comments WHERE post_id = :activity_id AND commenter_id 
					IN(SELECT id FROM users WHERE position = " . $this->user->getPosition() . " AND community_id = " . $this->user->getCommunityId() . ") ORDER BY time DESC LIMIT 5 ";
                    $db_query_comments = $this->database_connection->prepare($db_query_comments);
                    $db_query_comments->execute(array(":activity_id" => $activity_id));
                }
            }
        }
        $recordcomments = $db_query_comments->fetchAll(PDO::FETCH_ASSOC);
        $recordcomments = array_reverse($recordcomments);
        foreach ($recordcomments as $comment) {
            $rawtime = $comment['time'];
            $time = strtotime($rawtime);
            echo "<div class='single_comment_container'>";
            echo "<table id='post_comment_" . $activity_id . "' style='font-size: 0.9em;'><tr><td style='vertical-align:top;' rowspan='2'>";
            echo "<div class='post_comment_profile_picture post_comment_profile_picture_user' style='background-image:url(\"" . $this->user->getProfilePicture('chat', $comment['commenter_id']) . "\");'></div></td><td style='vertical-align:top;'>";
            echo "<a class='userdatabase_connection' href='user?id=" . urlencode(base64_encode($comment['commenter_id'])) . "'>";
            echo "<span class='user_preview user_preview_name post_comment_user_name' user_id='" . $comment['commenter_id'] . "'>" . $this->user->getName($comment['commenter_id']) . " </span></a>";
            echo "";
            echo "<span class='post_comment_text'>" . $comment['comment_text'] . "</span>
			</td></tr><tr><td colspan=2><span class='post_comment_time'>" . $this->system->humanTiming($time) . "</span>";
            echo "</tr></table></div><hr class='post_comment_seperator'>";
        }
    }

    public function getAssocFiles($activity_id = NULL) {
        $sql = "SELECT * FROM files WHERE id IN (SELECT file_id FROM activity_media WHERE activity_id = :activity_id) ORDER BY type;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
            array(
                ":activity_id" => $activity_id,
                ));
        $file_array = $sql->fetchAll(PDO::FETCH_ASSOC);
        $sql = "SELECT * FROM activity_media WHERE activity_id = :activity_id AND file_id IS NULL;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
            array(
                ":activity_id" => $activity_id,
                ));
        $web_array = $sql->fetchAll(PDO::FETCH_ASSOC);
        $result_array = array_merge($file_array, $web_array);
        return $result_array;
    }
    public function printFileItem($file_type, $file, $activity) {
        $post_classes   =   " class='post_feed_item ";
        $post_styles    =   " style='";
        $container      =   "<div";
        $post_content   =   "";

        if($file_type == "Audio") {
            $post_classes   .=  "post_media_audio";
            $post_styles    .=  " height:auto; ";
            $post_content   .=  $this->system->audioPlayer($file['thumb_filepath'], $file['name'], false, false);
        } else if($file_type == "Image") {
            $post_styles    .=  "background-image:url(\"".$file['thumb_filepath']."\")' onclick='initiateTheater(&quot;".$file['filepath']."&quot;, ".$activity['id'].");";
        } else {
            $post_classes   .=  "post_media_full";
            $post_styles    .=  "height:auto;";
            $post_content   .=  "<table style='height:100%;'><tr><td rowspan='3'>".
                    "<div class='post_media_webpage_favicon' style='background-image:url(&quot;" . $file['web_favicon'] . "&quot;);'></div></td>".
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>".
                    "<a class='user_preview_name' target='_blank' href='" . $file['URL'] . "'><span style='font-size:13px;'>" . $file['web_title'] ."</span></a></div></td></tr>".
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" . $file['web_description'] . "</span></td></tr></table>";
        }
        echo "<div ".$post_classes."' ".$post_styles."'>".$post_content."</div>";
    }

    function deletePost($post_id) {
        $school_query = "UPDATE activity SET visible = 0 WHERE id = :post_id; DELETE FROM activity_share WHERE activity_id = :post_id;";
        $school_query = $this->database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":post_id" => $post_id));
    }

    private function hasLikedPost($post_id) {
        $query = "SELECT id FROM votes WHERE user_id = :user_id AND post_id = :post_id AND vote_value = 1;";
        $query = $this->database_connection->prepare($query);
        $query->execute(array(":user_id" => $this->user->getId(), ":post_id" => $post_id));
        $num = $query->rowCount();

        if ($num == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    private function hasVotedPost($post_id){
        $query = "SELECT id FROM votes WHERE user_id = :user_id AND post_id = :post_id;";
        $query = $this->database_connection->prepare($query);
        $query->execute(array(":user_id" => $this->user->getId(), ":post_id" => $post_id));
        $num = $query->rowCount();

        if ($num == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function like($activity_id, $receiver_id) {
        $has_liked = $this->hasLikedPost($activity_id);
        $has_voted = $this->hasVotedPost($activity_id);
        if ($has_voted === false) {
            $insert_query = "INSERT INTO `votes` (post_id, user_id, vote_value) VALUES( :activity_id, :user_id, 1);";
            $insert_query = $this->database_connection->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $insert_query->execute(
                    array(":activity_id" => $activity_id, 
                        ":user_id" => $this->user->getId()
                    ));
            $this->notifyUserLike($activity_id, $receiver_id);
        } else {
            if($has_liked === false){
                $insert_query = "UPDATE votes SET vote_value = 1 WHERE post_id = :post_id AND user_id = :user_id;";
                $insert_query = $this->database_connection->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $insert_query->execute(
                        array(":post_id" => $activity_id, 
                            ":user_id" => $this->user->getId()
                        ));
            }
            else
            {
                $query = "UPDATE votes SET vote_value = 0 WHERE post_id = :post_id AND user_id = :user_id;";
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
        $who_liked_query = "INSERT INTO notification (post_id, receiver_id, sender_id, type) VALUES(:activity_id, :receiver_id, :sender_id, :type);";
        $who_liked_query = $this->database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $who_liked_query->execute(
                array(":activity_id" => $activity_id,
                    ":receiver_id" => $receiver_id,
                    ":sender_id" => $this->user->getId(),
                    ":type" => "like",
        ));
    }

    private function getLikeNumber($post_id){
        $who_liked_query = "SELECT id FROM `votes` WHERE vote_value = 1 AND post_id = :activity_id;";
        $who_liked_query = $this->database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $who_liked_query->execute(array(":activity_id" => $post_id));
        $like_count = $who_liked_query->rowCount();
        echo $like_count;
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $home = new Home;
    if (isset($_POST['activity_id'])) {
        if (isset($_POST['get_all'])) {
            $home->getComments($_POST['activity_id'], $_POST['get_all']);
        } else {
            $home->getComments($_POST['activity_id']);
        }
    }
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "deletePost") {
            $home->deletePost($_POST['post_id']);
        }
        if($_POST['action'] == 'like')
	{
            $home->like($_POST['id'], $_POST['receiver_id']);
	}
    }
}
?>