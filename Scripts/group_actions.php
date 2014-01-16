<?php

include_once('lock.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == "leave") {
        if ($database_connection->query("DELETE FROM group_member WHERE member_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "")) {
            die("success/" . urlencode(base64_encode($_POST['group_id'])));
        }
    }
    if ($_POST['action'] == "delete") {
        if ($database_connection->query("DELETE FROM `group` WHERE id =" . $_POST['group_id'] . "")) {
            if ($database_connection->query("DELETE FROM `group_member` WHERE group_id =" . $_POST['group_id'] . "")) {
                if ($database_connection->query("DELETE FROM `group_invite` WHERE group_id =" . $_POST['group_id'] . "")) {
                    if ($database_connection->query("DELETE FROM `group_chat` WHERE group_id =" . $_POST['group_id'] . "")) {
                        die("success/");
                    }
                }
            }
        }
    }
    if ($_POST['action'] == "abdicate") {
        if ($database_connection->query("INSERT INTO `election` (abdicate_id, abdicate_name, group_id) 
			VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['group_id'] . ");")) {
            if ($database_connection->query("INSERT INTO `activity` (user_id, user_gender, group_id, user_name, type)
				VALUES(" . $user->getId() . ", '" . $user['gender'] . "', " . $_POST['group_id'] . ", '" . $user->getName() . "', 'abdicate');")) {
                die("success/");
            }
            else {
                die(mysql_error());
            }
        }
        else {
            die(mysql_error());
        }
    }
    if ($_POST['action'] == "invite") {
        if ($database_connection->query("INSERT INTO `group_invite` (inviter_id, inviter_name, receiver_id, group_id) 
			VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['user_id'] . ", " . $_POST['group_id'] . ");")) {
            die('success/');
        }
        else {
            die($database_connection->query());
        }
    }
    if ($_POST['action'] == "join") {
        $sql = "SELECT id FROM group_member WHERE id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "";
        $sql = $database_connection->prepare($sql);
        $sql->execute();
        $number = $sql->rowCount();
        if ($number == 0) {
            $database_connection->query("INSERT INTO `group_member` (member_id, group_id) VALUES (" . $user->getId() . ", " . $_POST['group_id'] . ");");
        }
        $sql = "UPDATE `group_invite` SET invite_status = 2,`read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";";
        $sql = $database_connection->prepare($sql);
        $sql->execute();

        //die('success/');
    }
    if ($_POST['action'] == "reject") {
        if ($database_connection->query("UPDATE `group_invite` SET invite_status = 0, `read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";")) {
            die('success/');
        }
        else {
            die(mysql_error());
        }
    }
}
?>