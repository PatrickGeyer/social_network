<?php

class Home {

    private static $home = NULL;

    const SHOW_COMMENT_NUM = 5;

    public function __construct() {
        if (!class_exists('Registry')) {
            require 'declare.php';
        }
    }

    public static function getInstance() {
        if (self :: $home) {
            return self :: $home;
        }

        self :: $home = new Home();
        return self :: $home;
    }

    public static function set($args) {
        foreach ($args as $key => $value) {
            self :: $key = $value;
        }
    }

    function printFileList($type = NULL) {
        $files = Registry::get('files')->getList_r();
        echo "<table style='width:100%;'>";
        foreach ($files as $file) {
            if ($type == $file['type'] || $type === NULL || !isset($type) || $type == "") {
                $this->fileList($file);
            }
        }
        echo "</table>";
    }

    function fileList($file) {
        $file = Registry::get('files')->format_file($file);
        $data_file = json_encode($file, JSON_HEX_APOS);
        echo "<tr><td>";
        echo "<div class='file_item' id='home_file_list_item_" . $file['id'] . "' file_id='" . $file['id']
        . "' style='padding:0px;border:0px;margin:0px;top:0;min-height:40px;' class='file_search_option search_option ";
        if ($file['type'] == "Folder") {
            echo "file' ";
        }
        else {
            echo "file' ";
        }
        echo "data-file='" . $data_file . "'";
        echo ">";
        echo Registry::get('files')->filePreview($file, 'icon');
        echo "<span class='search_option_name'>" . Registry::get('system')->trimStr($file['name'], 15) . "</span>";
        //echo "<span class='search_option_info'> - ".$file['type']."</span>";
        echo "</div>";
        echo "</td></tr>";
    }

    public function getActivity($activity_query, $min_activity_id = 0) {
        $activities = $activity_query->fetchAll(PDO::FETCH_ASSOC);
        $final_activities = array();
        $count = count($activities);
        $max = $min_activity_id;
        foreach ($activities as $activity) {
            $final_activities[] = $this->homeify($activity);
            if ($activity['id'] > $max) {
                $max = $activity['id'];
            }
        }
        return $final_activities;
    }

    function getSingleActivity($activity_id) {
        $sql = "SELECT * FROM activity WHERE id = :activity_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":activity_id" => $activity_id
        ));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    function getLikes($activity) {
        $return = array();

        $who_liked_query = "SELECT user_id FROM `activity_vote` WHERE activity_id = :activity_id AND vote_value = 1;";
        $who_liked_query = Registry::get('db')->prepare($who_liked_query);
        $who_liked_query->execute(array(":activity_id" => $activity['id']));
        $who_liked_all = $who_liked_query->fetchAll(PDO::FETCH_ASSOC);

        $return['count'] = count($who_liked_all);
        $return['has_liked'] = $this->hasLikedPost($activity['id']);
        $return['user'] = array();

        $iteration = 0;
        foreach ($who_liked_all as $who_liked) {
            $return['user'][$iteration]['name'] = Registry::get('user')->getName($who_liked['user_id']);
            $return['user'][$iteration]['id'] = $who_liked['user_id'];
            $iteration++;
        }

        return $return;
    }

    function getStats($activity, $align = NULL) {
        $return = array();
        $return['like'] = $this->getLikes($activity);
        return $return;
    }

    function homeify($activity, $view = 'home', $activity_id = NULL) {
        $post_number = 0;
        $response = array();

        $response['view'] = $view;
        $response['status_text'] = $activity['status_text'];
        $response['type'] = $activity['type'];
        $response['id'] = $activity['id'];

        $response['user']['id'] = $activity['user_id'];
        $response['user']['name'] = Registry::get('user')->getName($activity['user_id']);
        $response['user']['pic'] = Registry::get('user')->getProfilePicture($activity['user_id']);

        $response['stats'] = $this->getStats($activity);
        $response['time'] = $activity['time'];
        $response['media'] = $this->getPostMedia($activity);

        $response['comment'] = $this->get_comments($activity['id']);

        return $response;
    }

    function getPostMedia($activity) {
        $assocFiles = $this->getAssocFiles($activity['id']);
        foreach ($assocFiles as $key => $file) {
            $assocFiles[$key] = Registry::get('files')->format_file($assocFiles[$key]);
            $assocFiles[$key]['activity']['id'] = Registry::get('files')->getActivity($assocFiles[$key]['id']);
            $assocFiles[$key]['activity']['comment'] = $this->get_comments($assocFiles[$key]['activity']['id']);
            $assocFiles[$key]['share'] = Registry::get('files')->get_shared($assocFiles[$key]['id']);
        }
        return $assocFiles;
    }

    function get_comments($activity_id, $min = 0, $max = Base::LARGEST_INT) {
        $between = " AND id BETWEEN :min AND :max AND visible = 1 ";
        $limit = $between . " ORDER BY id DESC LIMIT 5;";
        $format = 'all';
        $left = 0;
        $db_query_comments = "SELECT id FROM comment"
                . " WHERE activity_id = :activity_id" . $between;
        $db_query_comments = Registry::get('db')->prepare($db_query_comments);
        $db_query_comments->execute(array(
            ":activity_id" => $activity_id,
            ":min" => $min,
            ":max" => $max
        ));
        $numRows = $db_query_comments->rowCount();
        if ($numRows > Home::SHOW_COMMENT_NUM) {
            $format = 'top';
            $left = $numRows - Home::SHOW_COMMENT_NUM;
            $db_query_comments = "SELECT id, time, user_id, `comment` FROM comment"
                    . " WHERE activity_id = :activity_id " . $limit;
            $db_query_comments = Registry::get('db')->prepare($db_query_comments);
            $db_query_comments->execute(array(
                ":activity_id" => $activity_id,
                ":min" => $min,
                ":max" => $max
            ));
            $numRows = $db_query_comments->rowCount();
        }
        else {
            $db_query_comments = "SELECT id, time, user_id, `comment` FROM comment"
                    . " WHERE activity_id = :activity_id" . $limit;
            $db_query_comments = Registry::get('db')->prepare($db_query_comments);
            $db_query_comments->execute(array(
                ":activity_id" => $activity_id,
                ":min" => $min,
                ":max" => $max
            ));
            $numRows = $db_query_comments->rowCount();
        }
        $recordcomments = $db_query_comments->fetchAll(PDO::FETCH_ASSOC);
        $recordcomments = array_reverse($recordcomments);
        $return = array('comment' => array(), 'format' => $format, 'hidden' => $left);
        foreach ($recordcomments as $comment) {
            $return['comment'][] = $this->format_comment($comment);
        }
        return $return;
    }

    function submit_comment($comment, $post_id) {
        if ($comment != '') {
            Registry::get('db')->beginTransaction();
            $sql = "INSERT INTO comment (user_id, activity_id, comment) VALUES (" . Registry::get('user')->user_id . ", :post_id, :comment);";
            $sql = Registry::get('db')->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sql->execute(array(":post_id" => $post_id, ":comment" => $comment));
            $last_id = Registry::get('db')->lastInsertId();
            Registry::get('db')->commit();
            return $last_id;
        }
    }

    function get_comment($id) {
        $sql = "SELECT id, time, user_id, `comment` FROM comment WHERE id = :comment_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":comment_id" => $id
        ));
        return $this->format_comment($sql->fetch(PDO::FETCH_ASSOC));
    }

    function format_comment($comment) {
        $comment['user']['id'] = $comment['user_id'];
        $comment['user']['pic'] = Registry::get('user')->getProfilePicture($comment['user_id']);
        $comment['user']['name'] = Registry::get('user')->getName($comment['user_id']);
        $comment['text'] = $comment['comment'];
        $comment['like'] = array();
        $comment['like']['count'] = $this->comment_like_count($comment['id']);
        $comment['like']['has_liked'] = $this->has_liked_comment($comment['id']);
        return $comment;
    }

    public function getAssocFiles($activity_id = NULL) {
        $sql = "SELECT DISTINCT * FROM file AS file "
                . "LEFT JOIN (SELECT file_id, URL, web_title, web_description, web_favicon FROM activity_media WHERE activity_id = :activity_id)"
                . " AS act ON file.id = act.file_id "
                . "WHERE file.id IN (SELECT file_id FROM activity_media WHERE activity_id = :activity_id AND visible=1);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(
                array(
                    ":activity_id" => $activity_id,
        ));
        $file_array = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $file_array;
    }

    function deletePost($post_id) {
        $school_query = "UPDATE `activity` SET visible = 0 WHERE id = :post_id; "; //DELETE FROM activity_share WHERE activity_id = :post_id;";
        $school_query = Registry::get('db')->prepare($school_query);
        $school_query->execute(array(":post_id" => $post_id));
    }

    private function hasLikedPost($post_id) {
        $query = "SELECT id FROM activity_vote WHERE user_id = :user_id AND activity_id = :post_id AND vote_value = 1;";
        $query = Registry::get('db')->prepare($query);
        $query->execute(array(":user_id" => Registry::get('user')->getId(), ":post_id" => $post_id));
        $num = $query->rowCount();
        if ($num === 1) {
            return true;
        } else {
            return false;
        }
    }

    private function hasVotedPost($post_id) {
        $query = "SELECT id FROM activity_vote WHERE user_id = :user_id AND activity_id = :post_id;";
        $query = Registry::get('db')->prepare($query);
        $query->execute(array(":user_id" => Registry::get('user')->getId(), ":post_id" => $post_id));
        $num = $query->rowCount();

        if ($num == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public function like($activity_id) {
        $has_liked = $this->hasLikedPost($activity_id);
        $has_voted = $this->hasVotedPost($activity_id);
//        $string = '';
//        $string .= ("Voted:" . ($has_voted ? "true" : "false"). " / Liked: ". ($has_liked ? "true" : 'false'));
        if ($has_voted == false) {
            $insert_query = "INSERT INTO `activity_vote` (activity_id, user_id, vote_value) VALUES( :activity_id, :user_id, 1);";
            $insert_query = Registry::get('db')->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $insert_query->execute(
                    array(":activity_id" => $activity_id,
                        ":user_id" => Registry::get('user')->getId()
            ));
        } else {
//            $string .= " -- NOT FIRST TIME";
            if ($has_liked == false) {
                $insert_query = "UPDATE activity_vote SET vote_value = 1 WHERE activity_id = :post_id AND user_id = :user_id;";
                $insert_query = Registry::get('db')->prepare($insert_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $insert_query->execute(
                        array(":post_id" => $activity_id,
                            ":user_id" => Registry::get('user')->getId()
                ));
            } else {
                $query = "UPDATE activity_vote SET vote_value = 0 WHERE activity_id = :post_id AND user_id = :user_id;";
                $query = Registry::get('db')->prepare($query);
                $query->execute(
                        array(":post_id" => $activity_id,
                            ":user_id" => Registry::get('user')->getId()
                ));
            }
        }
//        return $string;
        return $this->getLikeNumber($activity_id);
    }

    private function notifyUserLike($activity_id) {
        $sql = "SELECT user_id FROM activity WHERE id = :activity_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":activity_id" => $activity_id));
        $receiver_id = $sql->fetchColumn();
        Registry::get('user')->notify("like", $receiver_id, $activity_id, NULL);
    }

    function updatePost($activity_id, $text, $files) {
        $sql = "UPDATE activity SET status_text = :text WHERE id = :id";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":text" => $text,
            ":id" => $activity_id
        ));
    }

    public function getLikeNumber($post_id) {
        $who_liked_query = "SELECT id FROM `activity_vote` WHERE vote_value = 1 AND activity_id = :activity_id;";
        $who_liked_query = Registry::get('db')->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $who_liked_query->execute(array(
            ":activity_id" => $post_id
        ));
        $like_count = $who_liked_query->rowCount();
        return strval($like_count);
    }

    function comment_like_count($comment_id) {
        $sql = "SELECT id FROM comment_vote WHERE comment_id = :comment_id AND visible=1;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":comment_id" => $comment_id
        ));
        $num = $sql->rowCount();
        return $num;
    }

    function has_liked_comment($comment_id) {
        $sql = "SELECT id FROM comment_vote WHERE user_id = :user_id AND comment_id = :comment_id AND visible=1;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":comment_id" => $comment_id
        ));
        $num = $sql->rowCount();
        if ($num === 0) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    function has_voted_comment($comment_id) {
        $sql = "SELECT id FROM comment_vote WHERE user_id = :user_id AND comment_id = :comment_id";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":comment_id" => $comment_id
        ));
        $num = $sql->rowCount();
        if ($num === 0) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    function comment_like($comment_id, $post_id) {
        if ($this->has_voted_comment($comment_id) == TRUE) {
            $sql = "UPDATE comment_vote SET visible = 1 WHERE user_id= :user_id AND comment_id = :element_id;";
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute(array(
                ":user_id" => Registry::get('user')->user_id,
                ":element_id" => $comment_id
            ));
        }
        else {
            $sql = "INSERT INTO comment_vote (user_id, comment_id) VALUES (:user_id, :comment_id)";
            // $user_sql = "SELECT user_id FROM comment WHERE id = :comment_id;";
            // $user_sql = Registry::get('db')->prepare($user_sql);
            // $user_sql->execute(array(
            //     ":comment_id" => $comment_id
            // ));
            // $user_id = $user_sql->fetchColumn();
            // Registry::get('user')->notify('comment_vote', $user_id, $post_id, $comment_id, 0, 0);
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute(array(
                ":user_id" => Registry::get('user')->user_id,
                ":comment_id" => $comment_id
            ));
        }
    }

    function remove_comment_like($comment_id) {
        $sql = "UPDATE comment_vote SET visible=0 WHERE user_id = :user_id AND comment_id = :comment_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":comment_id" => $comment_id
        ));

        $sql = "UPDATE notification SET visible=0 WHERE type='comment_like' AND post_id=:comment_id AND sender_id=:user_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":comment_id" => $comment_id
        ));
    }

    function delete_comment($comment_id) {
        $sql = "UPDATE comment SET visible = 0 WHERE id = :comment_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":comment_id" => $comment_id
        ));
    }

    function create_activity($status_text, $type) {
    	$status_text = strip_tags($status_text);
        Registry::get('db')->beginTransaction();
        $school_query = "INSERT INTO activity (user_id, status_text, type) "
                . "VALUES(:user_id, :status_text, '$type');";
        $school_query = Registry::get('db')->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":status_text" => $status_text,
        ));

        $lastInsertId = Registry::get('db')->lastInsertId();
        Registry::get('db')->commit();
        return $lastInsertId;
    }

    function add_files_to_activity($activity_id, $post_media_added_files) {
        foreach ($post_media_added_files as $file) {
            $num = NULL;
            if (!isset($file) || empty($file) || !is_array($file)) {
                $num = $file; //not set
            }
            else {
                $num = $file;
            }
            if (is_numeric($num)) {
                $media_query = "INSERT INTO activity_media (activity_id, file_id) VALUES (:activity_id, :file_id);";
                $options = array(
                    ":activity_id" => $activity_id,
                    ":file_id" => $num,
                );
            }
            else {
                Registry::get('db')->beginTransaction();
                $file_query = "INSERT INTO files (user_id, type) VALUES (:user_id, :type);";
                $file_query = Registry::get('db')->prepare($file_query);
                $file_query->execute(array(
                    ":user_id" => $user->user_id,
                    ":type" => "Webpage"
                ));
                $file_id = $dthis->atabase_connection->lastInsertId();

                $media_query = "INSERT INTO activity_media (activity_id, file_id, URL, web_title, web_description, web_favicon) "
                        . "VALUES (:activity_id, :file_id, :URL, :title, :description, :fav);";
                $options = array(
                    ":activity_id" => $activity_id,
                    ":file_id" => $file_id,
                    ":URL" => $file['path'],
                    ":title" => $file['info']['title'],
                    ":description" => $file['info']['description'],
                    ":fav" => $file['info']['favicon'],
                );
                Registry::get('db')->commit();
            }
            $media_query = Registry::get('db')->prepare($media_query);
            $media_query->execute($options);
        }
        return $activity_id;
    }

    function share_activity($activity, $group_id) {
        require_once('group.class.php');
        $this->group = Group::getInstance($args = array());
        if ($group_id == 'a') {
            foreach ($this->group->getUserGroups() as $single_group) {
                $school_query = "INSERT INTO activity_share (activity_id, group_id, direct) 
			VALUES(" . $activity . ", :group_id, 0);";
                $school_query = Registry::get('db')->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $school_query->execute(array(":group_id" => $single_group));
            }
        }
        else {
            $school_query = "INSERT INTO activity_share (activity_id, group_id, direct) 
		VALUES(" . $activity . ", :group_id, 1);";
            $school_query = Registry::get('db')->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $school_query->execute(array(":group_id" => $group_id));
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $home = Home::getInstance();
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "deletePost") {
            $home->deletePost($_POST['post_id']);
        }
        if ($_POST['action'] == 'like') {
            die($home->like($_POST['activity_id']));
        }
        if ($_POST['action'] == 'comment_vote') {
            if ($home->has_liked_comment($_POST['comment_id']) === true) {
                echo $home->remove_comment_like($_POST['comment_id']);
            }
            else {
                echo $home->comment_like($_POST['comment_id'], NULL);
            }
            echo $home->comment_like_count($_POST['comment_id']);
        }
        else if ($_POST['action'] == 'file_list') {
            die($home->printFileList($_POST['type']));
        }
        else if ($_POST['action'] == "get_comments") {
            die(json_encode($home->get_comments($_POST['activity_id'], $_POST['min'], $_POST['max'])));
        }
        else if ($_POST['action'] == "deleteComment") {
            $home->delete_comment($_POST['comment_id']);
        }
        else if ($_POST['action'] == "updatePost") {
            $files = array();
            if (isset($_POST['files'])) {
                $files = $_POST['files'];
            }
            $home->updatePost($_POST['activity_id'], $_POST['text'], $files);
        }
        else if ($_POST['action'] == "get_feed") {
            require_once 'entity.class.php';
            $entity = Entity::getInstance($args = array());
            $max = $min_activity_id = $user_id = $group_id = $filter = $activity_id = NULL;

            if (isset($_POST['min'])) {
                $min_activity_id = $_POST['min'];
            }
            if (isset($_POST['max'])) {
                $max = $_POST['max'];
            }

            if (isset($_POST['entity_type'])) {
                if ($_POST['entity_type'] == 'user') {
                    $user_id = $_POST['entity_id'];
                }
                else if ($_POST['entity_type'] == 'group') {
                    $group_id = $_POST['entity_id'];
                } else if($_POST['entity_type'] == 'activity') {
                    $activity_id = $_POST['entity_id'];
                }
            }
            else {
                $filter = $feed_id = 'a';
            }
            die(json_encode($home->getActivity($entity->getActivityQuery($filter, $group_id, $user_id, $min_activity_id, $max, $activity_id)), JSON_HEX_APOS));
        }
        else if ($_POST['action'] == 'submitComment') {
            die(json_encode($home->get_comment($home->submit_comment($_POST['comment_text'], $_POST['post_id']))));
        }
        else if ($_POST['action'] == "update_status") {
            $status_text = $_POST['status_text'];
            $post_media_added_files = (isset($_POST['post_media_added_files']) ? $_POST['post_media_added_files'] : array());
			$group = (empty($_POST['group_id']) ? 'a' : $_POST['group_id']);
            $type = 'Text';

            $home->share_activity(
                    $home->add_files_to_activity(
                            $home->create_activity($status_text, $type), $post_media_added_files)
                            , $group);
        }
        else if ($_POST['action'] == "add_files_to_activity") {
            die($home->add_files_to_activity($_POST['activity_id'], $_POST['media_added_files']));
        }
    }
}
?>