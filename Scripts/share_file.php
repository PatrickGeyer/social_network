<?php

include_once('lock.php');
$file_name = $_POST['file_name'];
$file_id = $_POST['file_id'];
$description = $_POST['comment'];
$receivers = $_POST['receivers'];

$database_connection->beginTransaction();
if (!$database_connection->query("INSERT INTO activity(user_id, type, description)
		VALUES(" . $user->getId() . ", '" . $file_id . "', '" . $description . "');")) {
    echo "error/";
}
$activity_id = $database_connection->lastInsertId();

foreach ($receivers as $receiver) {
    $split = explode('/', $receiver);
    if ($split[0] == "user") {
        $sql = "INSERT INTO file_share(user_id, file_id, receiver_id) VALUES (:user_id, :file_id, :receiver_id);";
        $sql = $database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $user->getId(), ":file_id" => $file_id, ":receiver_id" => $split[1]));
        if (!$database_connection->query("INSERT INTO activity_share(activity_id, receiver_id)
			 VALUES(" . $activity_id . ", " . $split[1] . ");")) {
            echo "error_share/";
        }
    }
    if ($split[0] == "group") {
        if (!$database_connection->query("INSERT INTO activity_share(activity_id, group_id)
			 VALUES(" . $activity_id . ", " . $split[1] . ");")) {
            echo "error_share/";
        }
        $sql = "INSERT INTO file_share(user_id, file_id, group_id) VALUES (:user_id, :file_id, :group_id);";
        $sql = $database_connection->prepare($sql);
        $sql->execute(array(":user_id" => $user->getId(), ":file_id" => $file_id, ":group_id" => $split[1]));
    }
}
$database_connection->commit();
?>