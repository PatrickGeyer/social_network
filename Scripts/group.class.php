<?php

require_once('entity.class.php');

class Group extends Entity {

    private static $group = NULL;

    public function __construct() {
        parent::__construct();
    }

    public static function getInstance($args = array()) {
        if (self :: $group) {
            return self :: $group;
        }

        self :: $group = new Group();
        return self :: $group;
    }

    function getName($id) {
        $user_query = "SELECT name FROM `group` WHERE id = :id;";
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getAbout($id) {
        $user_query = "SELECT about FROM `group` WHERE id = :id;";
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function isMember($user_id, $group_id) {
        $sql = "SELECT id FROM group_member WHERE user_id = :user_id AND group_id = :group_id";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":user_id" => $user_id, ":group_id" => $group_id));
        if ($sql->rowCount() == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    function getMembers($group_id) {
        $friend_query = "SELECT user_id FROM group_member WHERE group_id = :group_id AND user_id != :user_id";
        $friend_query = Registry::get('db')->prepare($friend_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $friend_query->execute(array(":user_id" => Registry::get('user')->user_id, ":group_id" => $group_id));
        return Registry::get('system')->array_values_r($friend_query->fetchAll(PDO::FETCH_ASSOC));
    }
    function getMembers_Connections($group_id) {
        $friend_query = "SELECT DISTINCT user_id as id FROM group_member WHERE group_id = :group_id AND user_id != :user_id AND user_id NOT IN"
                . "(SELECT IF(receiver_id = :user_id, user_id, receiver_id)as user_id FROM connection WHERE user_id = :user_id OR receiver_id = :user_id);";
        $friend_query = Registry::get('db')->prepare($friend_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $friend_query->execute(array(":user_id" => Registry::get('user')->user_id, ":group_id" => $group_id));
        return $friend_query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProfilePicture($id) {
        $user_query = "SELECT path as full, thumb_path as thumb, icon_path as icon FROM file WHERE id = (SELECT profile_picture FROM `group` WHERE id = :id);";
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(
        ":id" => $id
        ));
        $user = $user_query->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getFounderId($group_id) {
        $sql = "SELECT user_id FROM `group` WHERE id = :group_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":group_id" => $group_id));
        return $sql->fetchColumn();
    }

    function getUserGroups($user_id = null) {
        if (!isset($user_id) || $user_id == "") {
            $user_id = Registry::get('user')->user_id;
        }
        $user_query = "SELECT DISTINCT group_id FROM group_member WHERE user_id = :user_id;";
        $user_query = Registry::get('db')->prepare($user_query);
        $user_query->execute(array(":user_id" => $user_id));
        $usergroups = $user_query->fetchAll(PDO::FETCH_COLUMN);
        return $usergroups;
    }

    function createGroup($name, $about, $type, $receivers) {
        $type = 'public';

        if (strtolower($type) == "secret") {
            $type = 0;
        }
        else if (strtolower($type) == "school") {
            $type = 1;
        }
        else if (strtolower($type) == "public") {
            $type = 2;
        }

        Registry::get('db')->beginTransaction();
        $group_query = "INSERT INTO `group` (user_id, name, about, type) VALUES (:user_id, :group_name, :group_about, :group_type);";
        $group_query = Registry::get('db')->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $group_query->execute(array(":user_id" => Registry::get('user')->user_id, ":group_name" => $name, ":group_about" => $about, ":group_type" => $type));
        $new_group_id = Registry::get('db')->lastInsertId();
        Registry::get('db')->commit();

        Registry::get('db')->beginTransaction();
        $chat_sql = "INSERT INTO chat_room(name, type) VALUES(:name, :type);";
        $chat_sql = Registry::get('db')->prepare($chat_sql);
        $chat_sql->execute(array(
            ":name" => $name,
            ":type" => "group"
        ));
        $new_chat_id = Registry::get('db')->lastInsertId();
        Registry::get('db')->commit();

        $group_query = "INSERT INTO `group_member` (user_id, group_id) VALUES (:user_id, :new_group_id);";
        $group_query = Registry::get('db')->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $group_query->execute(array(":user_id" => Registry::get('user')->user_id, ":new_group_id" => $new_group_id));

        $chat_query = "INSERT INTO chat_member(chat_room, group_id) VALUES (:chat, :group);";
        $chat_query = Registry::get('db')->prepare($chat_query);
        $chat_query->execute(array(
            ":chat" => $new_chat_id,
            ":group" => $new_group_id
        ));

        if (is_array($receivers)) {
            foreach ($receivers as $type => $receiver) {
                foreach ($receiver as $single_receiver) {
                    $group_query = "INSERT INTO `group_invite` (sender_id, " . $type . "_id, group_id) VALUES (:user_id, :member_id, :new_group_id);";
                    $group_query = Registry::get('db')->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $group_query->execute(array(":user_id" => Registry::get('user')->user_id, ":member_id" => $single_receiver, ":new_group_id" => $new_group_id));
                }
            }
        }
        die("success/" . urlencode(base64_encode($new_group_id)));
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "leave") {
            if (Registry::get('db')->query("DELETE FROM group_member WHERE member_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "")) {
                die("success/" . urlencode(base64_encode($_POST['group_id'])));
            }
        }
        if ($_POST['action'] == "deleteG") {
            if (Registry::get('db')->query("DELETE FROM `group` WHERE id =" . $_POST['group_id'] . ";DELETE FROM `group_member` WHERE group_id =" . $_POST['group_id'] . ";DELETE FROM `group_invite` WHERE group_id =" . $_POST['group_id'] . ";DELETE FROM `group_chat` WHERE group_id =" . $_POST['group_id'] . ";")) {
                die("success/");
            }
        }
        if ($_POST['action'] == "abdicate") {
            if (Registry::get('db')->query("INSERT INTO `election` (abdicate_id, abdicate_name, group_id) 
				VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['group_id'] . ");")) {
                if (Registry::get('db')->query("INSERT INTO `activity` (user_id, user_gender, group_id, user_name, type)
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
            if (Registry::get('db')->query("INSERT INTO `group_invite` (inviter_id, inviter_name, receiver_id, group_id) 
				VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['user_id'] . ", " . $_POST['group_id'] . ");")) {
                die('success/');
            }
            else {
                die(Registry::get('db')->query());
            }
        }
        if ($_POST['action'] == "join") {
            $sql = "SELECT id FROM group_member WHERE user_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "";
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute();
            $number = $sql->rowCount();
            if ($number == 0) {
                Registry::get('db')->query("INSERT INTO `group_member` (user_id, group_id) VALUES (" . $user->getId() . ", " . $_POST['group_id'] . ");");
            }
            $sql = "UPDATE `group_invite` SET invite_status = 2,`read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";";
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute();

            //die('success/');
        }
        if ($_POST['action'] == "reject") {
            if (Registry::get('db')->query("UPDATE `group_invite` SET invite_status = 0, `read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";")) {
                die('success/');
            }
        }
        if ($_POST['action'] == "create") {
            $name = NULL;
            $about = NULL;
            $type = NULL;
            $receivers = NULL;

            if (isset($_POST['group_name'])) {
                $name = $_POST['group_name'];
            }
            if (isset($_POST['group_about'])) {
                $about = $_POST['group_about'];
            }
            if (isset($_POST['group_type'])) {
                $type = $_POST['group_type'];
            }
            if (isset($_POST['invited_members'])) {
                $receivers = $_POST['invited_members'];
            }
            $group->createGroup($name, $about, $type, $receivers);
        }
        if ($_POST['action'] == "leave") {
            if (Registry::get('db')->query("DELETE FROM group_member WHERE member_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "")) {
                die("success/" . urlencode(base64_encode($_POST['group_id'])));
            }
        }
        if ($_POST['action'] == "delete") {
            if (Registry::get('db')->query("DELETE FROM `group` WHERE id =" . $_POST['group_id'] . "")) {
                if (Registry::get('db')->query("DELETE FROM `group_member` WHERE group_id =" . $_POST['group_id'] . "")) {
                    if (Registry::get('db')->query("DELETE FROM `group_invite` WHERE group_id =" . $_POST['group_id'] . "")) {
                        if (Registry::get('db')->query("DELETE FROM `group_chat` WHERE group_id =" . $_POST['group_id'] . "")) {
                            die("success/");
                        }
                    }
                }
            }
        }
        if ($_POST['action'] == "abdicate") {
            if (Registry::get('db')->query("INSERT INTO `election` (abdicate_id, abdicate_name, group_id)"
                    . "VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['group_id'] . ");")) {
                if (Registry::get('db')->query("INSERT INTO `activity` (user_id, user_gender, group_id, user_name, type)
 				VALUES(" . $user->getId() . ", '" . $user['gender'] . "', " . $_POST['group_id'] . ", '" . $user->getName() . "', 'abdicate');")) {
                    die("success/");
                }
            }
        }
        if ($_POST['action'] == "invite") {
            if (Registry::get('db')->query("INSERT INTO `group_invite` (inviter_id, inviter_name, receiver_id, group_id) 
 			VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['user_id'] . ", " . $_POST['group_id'] . ");")) {
                die('success/');
            }
        }
        if ($_POST['action'] == "join") {
            $sql = "SELECT id FROM group_member WHERE id = " . $user->user_id . " AND group_id = " . $_POST['group_id'] . "";
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute();
            $number = $sql->rowCount();
            if ($number == 0) {
                Registry::get('db')->query("INSERT INTO `group_member` (user_id, group_id) VALUES (" . $user->user_id . ", " . $_POST['group_id'] . ");");
            }
            $sql = "UPDATE `group_invite` SET invite_status = 2,`read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";";
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute();

            //die('success/');
        }
        if ($_POST['action'] == "reject") {
            if (Registry::get('db')->query("UPDATE `group_invite` SET invite_status = 0, `read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";")) {
                die('success/');
            }
        }
    }
}
?>