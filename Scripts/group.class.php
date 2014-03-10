<?php

include_once('database.class.php');
include_once('user.class.php');

class Group {

    private static $group = NULL;
    private $user_id;
    private $database_connection;
    private $user;

    public function __construct() {
        $this->database_connection = Database::getConnection();
        if(isset($_COOKIE['id'])) {
            $this->user_id = base64_decode($_COOKIE['id']);
            $this->user = User::getInstance();
        }
        return true;
    }

    public static function getInstance() {
        if (self :: $group) {
            return self :: $group;
        }

        self :: $group = new Group();
        return self :: $group;
    }

    function getId() {
        return base64_decode($_COOKIE['id']);
    }

    function getGroupName($id) {
        $user_query = "SELECT name FROM `group` WHERE id = :id;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getAbout($id) {
        $user_query = "SELECT about FROM `group` WHERE id = :id;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function isMember($user_id, $group_id) {
        $sql = "SELECT id FROM group_member WHERE user_id = :user_id AND group_id = :group_id";
        $sql = $this->database_connection->prepare($sql);
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
        $friend_query = $this->database_connection->prepare($friend_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $friend_query->execute(array(":user_id" => $this->user->user_id, ":group_id" => $group_id));
        return $friend_query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getProfilePicture($size = "chat", $id) {
        $user_query = "SELECT profile_picture FROM `group` WHERE id = :id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $this->user_id));
        $user = $user_query->fetchColumn();
        if (!empty($user) || $user != "") {
            return $user;
        }
        else {
            return "Images/group-default-chat.png";
        }
    }

    public function getFounderId($group_id) {
        $sql = "SELECT user_id FROM `group` WHERE id = :group_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(":group_id" => $group_id));
        return $sql->fetchColumn();
    }

    function getUserGroups($user_id = null) {
        if (!isset($user_id) || $user_id == "") {
            $user_id = $this->user_id;
        }
        $user_query = "SELECT group_id FROM group_member WHERE user_id = :user_id;";
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => $user_id));
        $usergroups = $user_query->fetchAll(PDO::FETCH_COLUMN);
        return $usergroups;
    }
    
    function createGroup($name, $about, $type, $receivers) {
        $type = 'public';
  
	if(strtolower($type) == "secret")
	{
		$type = 0;
	}
	else if(strtolower($type) == "school")
	{
		$type = 1;
	}
	else if(strtolower($type) == "public")
	{
		$type = 2;
	}
        
		$this->database_connection->beginTransaction();
        $group_query = "INSERT INTO `group` (user_id, name, about, type) VALUES (:user_id, :group_name, :group_about, :group_type);";
        $group_query = $this->database_connection->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $group_query->execute(array(":user_id" => $this->user->user_id, ":group_name" => $name, ":group_about" => $about, ":group_type" => $type));
		$new_group_id = $this->database_connection->lastInsertId();
		$this->database_connection->commit();
		
		$this->database_connection->beginTransaction();
		$chat_sql = "INSERT INTO chat_room(name, type) VALUES(:name, :type);";
		$chat_sql = $this->database_connection->prepare($chat_sql);
		$chat_sql->execute(array(
			":name" => $name,
			":type" => "group"
		));
		$new_chat_id = $this->database_connection->lastInsertId();
		$this->database_connection->commit();
		
        $group_query = "INSERT INTO `group_member` (user_id, group_id) VALUES (:user_id, :new_group_id);";
        $group_query = $this->database_connection->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $group_query->execute(array(":user_id" => $this->user->user_id, ":new_group_id" => $new_group_id));
        
		$chat_query = "INSERT INTO chat_member(chat_room, group_id) VALUES (:chat, :group);";
		$chat_query = $this->database_connection->prepare($chat_query);
		$chat_query->execute(array(
			":chat" => $new_chat_id,
			":group" => $new_group_id
		));
        
        if(is_array($receivers)) {
            foreach ($receivers as $type => $receiver) {
                foreach ($receiver as $single_receiver) {
                    $group_query = "INSERT INTO `group_invite` (sender_id, ".$type."_id, group_id) VALUES (:user_id, :member_id, :new_group_id);";
                    $group_query = $this->database_connection->prepare($group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $group_query->execute(array(":user_id" => $this->user->user_id, ":member_id" => $single_receiver, ":new_group_id" => $new_group_id));
                    
                }
            }
        }
        die("success/" . urlencode(base64_encode($new_group_id)));
    }
    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('user.class.php');
    include_once('database.class.php');
    $database_connection = Database::getConnection();
    $user = User::getInstance();
    if (isset($_POST['action'])) {
        include_once("lock.php");
        if ($_POST['action'] == "leave") {
            if ($database_connection->query("DELETE FROM group_member WHERE member_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "")) {
                die("success/" . urlencode(base64_encode($_POST['group_id'])));
            }
        }
        if ($_POST['action'] == "deleteG") {
            if ($database_connection->query("DELETE FROM `group` WHERE id =" . $_POST['group_id'] . ";DELETE FROM `group_member` WHERE group_id =" . $_POST['group_id'] . ";DELETE FROM `group_invite` WHERE group_id =" . $_POST['group_id'] . ";DELETE FROM `group_chat` WHERE group_id =" . $_POST['group_id'] . ";")) {
                die("success/");
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
            $sql = "SELECT id FROM group_member WHERE user_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "";
            $sql = $database_connection->prepare($sql);
            $sql->execute();
            $number = $sql->rowCount();
            if ($number == 0) {
                $database_connection->query("INSERT INTO `group_member` (user_id, group_id) VALUES (" . $user->getId() . ", " . $_POST['group_id'] . ");");
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
        }
        // if($_POST['action'] == "create") {
//             $name = NULL;
//             $about = NULL;
//             $type = NULL;
//             $receivers = NULL;
//             
//             if(isset($_POST['group_name'])) {
//                 $name = $_POST['group_name'];
//             }
//             if(isset($_POST['group_about'])) {
//                 $about = $_POST['group_about'];
//             }
//             if(isset($_POST['group_type'])) {
//                 $type = $_POST['group_type'];
//             }
//             if(isset($_POST['invited_members'])) {
//                 $receivers = $_POST['invited_members'];
//             }
//             $group->createGroup($name, $about, $type, $receivers);
//         }
//   	if ($_POST['action'] == "leave") {
//         if ($database_connection->query("DELETE FROM group_member WHERE member_id = " . $user->getId() . " AND group_id = " . $_POST['group_id'] . "")) {
//             die("success/" . urlencode(base64_encode($_POST['group_id'])));
//         }
//     }
//     if ($_POST['action'] == "delete") {
//         if ($database_connection->query("DELETE FROM `group` WHERE id =" . $_POST['group_id'] . "")) {
//             if ($database_connection->query("DELETE FROM `group_member` WHERE group_id =" . $_POST['group_id'] . "")) {
//                 if ($database_connection->query("DELETE FROM `group_invite` WHERE group_id =" . $_POST['group_id'] . "")) {
//                     if ($database_connection->query("DELETE FROM `group_chat` WHERE group_id =" . $_POST['group_id'] . "")) {
//                         die("success/");
//                     }
//                 }
//             }
//         }
//     }
//     if ($_POST['action'] == "abdicate") {
//         if ($database_connection->query("INSERT INTO `election` (abdicate_id, abdicate_name, group_id) 
// 			VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['group_id'] . ");")) {
//             if ($database_connection->query("INSERT INTO `activity` (user_id, user_gender, group_id, user_name, type)
// 				VALUES(" . $user->getId() . ", '" . $user['gender'] . "', " . $_POST['group_id'] . ", '" . $user->getName() . "', 'abdicate');")) {
//                 die("success/");
//             }
//             else {
//                 die(mysql_error());
//             }
//         }
//         else {
//             die(mysql_error());
//         }
//     }
//     if ($_POST['action'] == "invite") {
//         if ($database_connection->query("INSERT INTO `group_invite` (inviter_id, inviter_name, receiver_id, group_id) 
// 			VALUES (" . $user->getId() . ",'" . $user->getName() . "', " . $_POST['user_id'] . ", " . $_POST['group_id'] . ");")) {
//             die('success/');
//         }
//         else {
//             die($database_connection->query());
//         }
//     }
//     if ($_POST['action'] == "join") {
//         $sql = "SELECT id FROM group_member WHERE id = " . $user->user_id . " AND group_id = " . $_POST['group_id'] . "";
//         $sql = $database_connection->prepare($sql);
//         $sql->execute();
//         $number = $sql->rowCount();
//         if ($number == 0) {
//             $database_connection->query("INSERT INTO `group_member` (user_id, group_id) VALUES (" . $user->user_id . ", " . $_POST['group_id'] . ");");
//         }
//         $sql = "UPDATE `group_invite` SET invite_status = 2,`read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";";
//         $sql = $database_connection->prepare($sql);
//         $sql->execute();
// 
//         //die('success/');
//     }
//     if ($_POST['action'] == "reject") {
//         if ($database_connection->query("UPDATE `group_invite` SET invite_status = 0, `read`=1,seen=1 WHERE id = " . $_POST['invite_id'] . ";")) {
//             die('success/');
//         }
//         else {
//             die(mysql_error());
//         }
//     }
    }
}
?>