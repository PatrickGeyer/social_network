<?php

include_once('database.class.php');
include_once('user.class.php');

class Notification {

    private static $notifiction = NULL;
    private $user;
    protected $database_connection;

    public function __construct() {
        $this->user = User::getInstance();
        $this->database_connection = Database::getConnection();
    }

    public static function getInstance() {
        if (self :: $notifiction) {
            return self :: $notifiction;
        }

        self :: $notifiction = new Notification();
        return self :: $notifiction;
    }

    function getMessage($type = null, $id = null) {
        if (!isset($type)) {
            $user_query = "SELECT sender_id, message, thread, time, u_id, id FROM messages WHERE thread IN "
                    . "(SELECT thread_id FROM message_share WHERE receiver_id = :user_id) "
                    . "AND id IN (SELECT max(id) FROM messages GROUP BY thread) "
                    . "ORDER BY id DESC;";
        }
        else if ($type == 'thread') {
            $user_query = "SELECT sender_id, message, thread, time, u_id, id FROM messages WHERE thread IN (SELECT thread_id FROM message_share WHERE receiver_id = :user_id AND thread_id = " . $id . ") 
             ORDER BY id ASC;";
        }
        else {
            $user_query = "SELECT sender_id, message, thread, time, u_id, id FROM messages WHERE id = " . $id . " ORDER BY id DESC;";
        }
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => $this->user->user_id));
        $user1 = $user_query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($user1 as &$message) {
            $message_read_query = "SELECT `read`, seen, receiver_id FROM message_share WHERE thread_id = " . $message['thread'] . " AND receiver_id = " . base64_decode($_COOKIE['id']) . ";";
            $message_read_query = $this->database_connection->prepare($message_read_query);
            $message_read_query->execute();
            $read = $message_read_query->fetch(PDO::FETCH_ASSOC);
            $message['read'] = $read['read'];
            $message['seen'] = $read['seen'];
            $message['receiver_id'] = $read['receiver_id'];
        }
        return $user1;
    }

    function getMessageNum() {
        $user_query = "SELECT id FROM message_share WHERE receiver_id = :user_id AND `seen` = 0;";
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return $user;
    }

    function markMessageRead($type = 'thread', $id) {
        if ($type == "thread") {
            $thread = $id;
        }
        $sql = "UPDATE message_share SET seen=1, `read`=1 WHERE thread_id = " . $id . " AND receiver_id=" . base64_decode($_COOKIE['id']) . ";";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute();
    }

    function markAllMessageSeen() {
        $sql = "UPDATE message_share SET seen=1 WHERE receiver_id=" . base64_decode($_COOKIE['id']) . ";";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute();
    }

    public function getReceivers($thread_id, $style = false) {
        $sql = "SELECT id FROM users WHERE id IN(SELECT DISTINCT receiver_id FROM message_share WHERE thread_id = :thread_id);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
                array(
                    ":thread_id" => $thread_id,
        ));
        if ($style !== false) {
            return $this->styleReceiverList($sql->fetchALL(PDO::FETCH_COLUMN), $style);
        }
        else {
            return $sql->fetchAll(PDO::FETCH_COLUMN);
        }
    }   
    
    public function getMessagePicture($size, $thread) {
        $all_receivers = $this->getReceivers($thread, false);
        foreach ($all_receivers as $key => $receiver) {
            if($receiver == $this->user->user_id) {
                unset($all_receivers[$key]);
            }
        }
        $receivers = array_values($all_receivers);
        $return = '';
        $count = count($receivers);
        
        $width = array();
        $height = array();
        
        if($count === 1) {
            $width[0] = "100%";
            $height[0] = "100%";
        } 
        else if($count === 2) {
            $width[0] = "50%";
            $height[0] = "100%";
            $width[1] = "50%";
            $height[1] = "100%";
        } 
        else if($count === 3) {
            $width[0] = "50%";
            $height[0] = "50%";
            $width[1] = "50%";
            $height[1] = "50%";
            $width[2] = "100%";
            $height[2] = "50%";
        }
        else {
            $width[0] = "50%";
            $height[0] = "50%";
            $width[1] = "50%";
            $height[1] = "50%";
            $width[2] = "50%";
            $height[2] = "50%";
            $width[3] = "50%";
            $height[3] = "50%";
        }
        foreach ($receivers as $key => $receiver) {
            if($key < 4) {
                $return .= $this->img($this->user->getProfilePicture('chat', $receiver), $width[$key], $height[$key]);
            }
        }
        return $return;
    }
    function img($src, $width = "'auto'", $height = "'auto'", $styles = NULL) {
        return "<div style='display:inline-block;background-size:cover;background-image: url(\"".$src."\");height:" . $height . "; width:" . $width . "'></div>";
    }

    private function styleReceiverList($list, $type) {
        $return = NULL;
        $num = count($list);
        foreach ($list as $key => $name) {
            if($this->user->user_id != $name) {
                if($type == "header") {
                    $return .= "<a href='user?id=" . urlencode(base64_encode($name)) 
                            . "'><span style='margin-right:5px;' "
                            . " class='message_convo_receiver user_preview' user_id='" . $name . "'>";
                    $return .= $this->user->getName($name, 1);
                    if ($num - 1 != $key) {
                        $return .= ",";
                    }
                    $return .= "</span></a>";
                }
                else if($type == "list") {
                    $name = $this->user->getName($name, 1);
                    if ($num - 1 != $key) {
                        $name .= ",";
                    }
                    $return .= "" . $name . "";
                }
            }
        }
        return $return;
    }

    public function sendMessage($message, $receivers = null, $thread) {
        if ($receivers == NULL) {
            $receivers = $this->getReceivers($thread, false);
        }
        if(!in_array($this->user->user_id, $receivers)) {
            array_push($receivers, $this->user->user_id);
        }
        array_unique($receivers);
        asort($receivers);
        $this->database_connection->beginTransaction();
        $thread = $this->getThreadNum($receivers);

        foreach ($receivers as $receiver) {
            $this->insertMessageShare($receiver, $message, $thread);
        }
        $this->insertMessage($message, $thread, $receivers);
        $this->database_connection->commit();
    }

    private function insertMessage($message, $thread, $receivers) {
        $message_query = "INSERT INTO messages (sender_id, message, thread, time, u_id) VALUES (:user_id, :message, :thread_id, :time, :u_id);";
        $message_query = $this->database_connection->prepare($message_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $message_query->execute(
                array(
                    ":user_id" => base64_decode($_COOKIE['id']),
                    ":message" => $message, ":thread_id" => $thread,
                    ":time" => time(),
                    ":u_id" => implode(',', $receivers),
        ));
    }

    private function insertMessageShare($receiver, $message, $thread) {
        $message_query = "INSERT INTO  message_share(receiver_id, sender_id, thread_id, `seen`, `read`) VALUES (:receiver_id, :sender_id, :thread_id, :seen, :read)";
        $message_query = $this->database_connection->prepare($message_query);
        $message_query->execute(
                array(
                    ":receiver_id" => $receiver,
                    ":sender_id" => $this->user->getId(),
                    ":thread_id" => $thread,
                    ":seen" => ($receiver == $this->user->getId() ? 1 : 0),
                    ":read" => ($receiver == $this->user->getId() ? 1 : 0),
        ));
    }

    private function getThreadNum($receivers) {
        $getthreadvalue = "SELECT MAX(thread) FROM messages;";

        $getthreadvalue = $this->database_connection->prepare($getthreadvalue);
        $getthreadvalue->execute();
        $thread = $getthreadvalue->fetchColumn() + 1;

        $sql = "SELECT thread FROM messages WHERE u_id = :u_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
                array(
                    ":u_id" => implode(',', $receivers),
        ));
        $num = $sql->rowCount();

        if ($num > 0) {
            $thread = $sql->fetchColumn();
        }
        return $thread;
    }

    public function getRecentThread($user_id = NULL) {
        if($user_id === NULL) {
            $sql = "SELECT thread FROM messages WHERE thread IN "
                    . "(SELECT thread_id FROM message_share WHERE receiver_id = :user_id)"
                    . " ORDER BY time DESC LIMIT 1;";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(
                    array(
                        ":user_id" => $this->user->user_id,
            ));
            $result = $sql->fetchColumn();
            if(!isset($result)) {
                $result = 'false';
            }
            return $result;
        }
        else {
            
        }
    }

    function getNotification() {
        $user_query = "SELECT * FROM notification WHERE receiver_id = :user_id AND sender_id != :user_id ORDER BY time DESC;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->fetchAll();
        return $user;
    }

    function markAllNotificationsSeen() {
        $this->database_connection->query("UPDATE notification SET seen = 1 WHERE receiver_id = " . base64_decode($_COOKIE['id']) . "");
    }

    function getNotificationNum() {
        $user_query = "SELECT receiver_id FROM notification WHERE receiver_id = :user_id AND sender_id != :user_id AND `seen` = 0";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return $user;
    }

    function markNotificationRead($id) {
        $this->database_connection->query("UPDATE notification SET `read`= 1 WHERE id = " . $id . "");
    }

    function getNetwork() {
        $user_query = "SELECT * FROM group_invite WHERE receiver_id = :user_id ORDER BY time DESC;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->fetchAll();
        return $user;
    }

    function getNetworkNum() {
        $user_query = "SELECT id FROM group_invite WHERE receiver_id = :user_id AND `seen` = 0 AND invite_status = 1;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return $user;
    }

    function markAllNetworkSeen() {
        $this->database_connection->query('UPDATE group_invite SET seen=1 WHERE receiver_id = ' . base64_decode($_COOKIE['id']) . ';');
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $notify = new Notification();
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "mark") {
            if ($_POST['type'] == "message") {
                $notify->markAllMessageSeen();
            }
            else if ($_POST['type'] == "notification") {
                $notify->markAllNotificationsSeen();
            }
            else if ($_POST['type'] == "network") {
                $notify->markAllNetworkSeen();
            }
        }
        else if ($_POST['action'] == "markNotificationRead") {
            $notify->markNotificationRead($_POST['id']);
        }
        else if ($_POST['action'] == 'sendMessage') {
            if(isset($_POST['thread_id'])) {
                $thread = $_POST['thread_id'];
            }
            else {
                $thread = NULL;
            }
            $receivers = (isset($_POST['receivers']) ? $_POST['receivers'] : null);
            $notify->sendMessage($_POST['message'], $receivers, $thread);
        }
        else if($_POST['action'] === "alert_num") {
            $return = array();
            $return['message'] = $notify->getMessageNum();
            $return['network'] = $notify->getNetworkNum();
            $return['notification'] = $notify->getNotificationNum();
            $return = json_encode($return);
            die($return);
        }
    }
}
?>