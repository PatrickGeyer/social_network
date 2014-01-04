<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('lock.php');
    $status_text = $_POST['status_text'];
    $status_text = strip_tags($status_text);
    $status_text= preg_replace( 
        "/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", 
        "<a href=\"\\0\" target=\"blank\" class=\"post_feed_link\">\\0</a>", $status_text);
    $post_media_added_files = (isset($_POST['post_media_added_files']) ? $_POST['post_media_added_files']: array());
    $status_text = nl2br($status_text);

    $database_connection->beginTransaction();
    try {
        $school_query = "INSERT INTO activity (user_id, status_text, type) 
		VALUES(:user_id, :status_text, 'text');";
        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":user_id" => $user->getId(), ":status_text" => $status_text));
    } catch (PDOException $e) {
        die("Error:" . $e->getMessage());
    }
    $lastInsertId = $database_connection->lastInsertId();

    if ($_POST['group_id'] == "s") {
        $school_query = "INSERT INTO activity_share (activity_id, community_id, direct) 
		VALUES(" . $database_connection->lastInsertId() . ", :community_id, 1);";
        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":community_id" => $user->getCommunityId()));
    }
    else if ($_POST['group_id'] == "y") {
        $school_query = "INSERT INTO activity_share (activity_id, community_id, year, direct) 
		VALUES(" . $lastInsertId . ", :community_id, :user_year, 1);";
        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":user_year" => $user->getPosition(), ":community_id" => $user->getCommunityId()));
    }
    elseif ($_POST['group_id'] == 'a') {

        $school_query = "INSERT INTO activity_share (activity_id, community_id, direct) 
		VALUES(" . $lastInsertId . ", :community_id, 0);";
        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":community_id" => $user->getCommunityId()));

        $school_query = "INSERT INTO activity_share (activity_id, year, direct) 
		VALUES(" . $lastInsertId . ", :user_year, 0);";
        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":user_year" => $user->getPosition()));

        foreach ($group->getUserGroups() as $single_group) {
            $school_query = "INSERT INTO activity_share (activity_id, group_id, direct) 
			VALUES(" . $lastInsertId . ", :group_id, 0);";
            $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $school_query->execute(array(":group_id" => $single_group));
        }
    }
    else {
        $school_query = "INSERT INTO activity_share (activity_id, group_id, direct) 
		VALUES(" . $lastInsertId . ", :group_id, 1);";
        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $school_query->execute(array(":group_id" => $_POST['group_id']));
    }
    foreach ($post_media_added_files as $file) {
        if(is_numeric($file['file_id'])) {
            $media_query = "INSERT INTO activity_media (activity_id, file_id) VALUES (:activity_id, :file_id);";
            $options = array(
                ":activity_id" => $lastInsertId,
                ":file_id" => $file['file_id'],
            );
            
        } else {
            $media_query = "INSERT INTO activity_media (activity_id, URL, web_title, web_description, web_favicon) VALUES (:activity_id, :URL, :title, :description, :fav);";
            $options = array(
                ":activity_id" => $lastInsertId,
                ":URL" => $file['path'],
                ":title" => $file['info']['title'],
                ":description" => $file['info']['description'],
                ":fav" => $file['info']['favicon'],
            );
        }
        $media_query = $database_connection->prepare($media_query);
        $media_query->execute($options);
    }
    $database_connection->commit();
    die("200");
}
?>