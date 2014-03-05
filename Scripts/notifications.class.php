<?php

include_once('database.class.php');
include_once('user.class.php');
include_once('group.class.php');
include_once('phrase.class.php');
include_once('system.class.php');

class Notification {

    private static $notifiction = NULL;
    private $user;
    private $group;
    private $phrase;
    private $system;
    protected $database_connection;

    public function __construct() {
        $this->user = User::getInstance();
        $this->database_connection = Database::getConnection();
        $this->group = new Group();
        $this->phrase = new Phrase();
        $this->system = System::getInstance();
    }

    public static function getInstance() {
        if (self :: $notifiction) {
            return self :: $notifiction;
        }

        self :: $notifiction = new Notification();
        return self :: $notifiction;
    }

    function getMessage($type = null, $id = null, $oldest = 0, $newest = 999999999) {
        if(is_null($oldest)) $oldest = 0;
        if(is_null($newest)) $newest = 9999999999999;
        if (!isset($type)) {
            $user_query = "SELECT user_id, message, thread, time, u_id, id FROM message WHERE thread IN "
                    . "(SELECT `thread` FROM message_share WHERE user_id = :user_id AND visible=1) "
                    . "AND id IN (SELECT max(id) FROM message GROUP BY thread) "
                    . " AND id BETWEEN :oldest AND :newest ORDER BY id DESC;";
        }
        else if ($type == 'thread') {
            $user_query = "SELECT user_id, message, thread, time, u_id, id FROM message WHERE thread IN "
            . "(SELECT `thread` FROM message_share WHERE user_id = :user_id AND `thread` = " . $id . ") "
            . " AND id BETWEEN :oldest AND :newest ORDER BY id ASC;";
        }
        else {
            //$user_query = "SELECT user_id, message, thread, time, u_id, id FROM message WHERE id = " . $id . ";";
        }
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(
            ":user_id" => $this->user->user_id,
            ":oldest" => $oldest,
            ":newest" => $newest
            ));
        $user1 = $user_query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($user1 as &$message) {
            $message_read_query = "SELECT `read`, seen, user_id FROM message_share WHERE `thread` = " . $message['thread'] . " AND user_id = " . base64_decode($_COOKIE['id']) . ";";
            $message_read_query = $this->database_connection->prepare($message_read_query);
            $message_read_query->execute();
            $read = $message_read_query->fetch(PDO::FETCH_ASSOC);
            $message['read'] = $read['read'];
            $message['seen'] = $read['seen'];
            $message['user'] = $this->user->get_user_preview($message['user_id']);
            $message['time'] = $this->system->humanTiming($message['time']);
            //$message['user_id'] = $read['user_id'];
        }
        return $user1;
    }
    
    function getMessageNew($thread) {
        $sql = "SELECT * FROM message WHERE thread = :thread and time >= :time;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":thread" => $thread,
            ":time" => $time
        ));
        
    }

    function getMessageNum() {
        $user_query = "SELECT id FROM message_share WHERE user_id = :user_id AND `seen` = 0;";
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return $user;
    }

    function getMessageList() {

        $message_count = $this->getMessageNum();
        $messages = $this->getMessage();
        foreach ($messages as $message) {
            $participants = $this->getReceivers($message['thread'], 'list');
            $picture = $this->user->getProfilePicture('chat', $message['user_id']);
            $names = '';//$this->styleReceiverList($participants, 'list');
            echo "<li class='";
            if ($message['read'] == 0) {
                echo "messageunread";
            }
            else {
                echo "message";
            }
            echo "'><a class='message' href='message?thread=" . $message['thread'] . "&id=" . $message['id'] . "'>"
            . "<div style='display:table-row;'>"
            . "<div class='notification_user_image'>"
            . $this->getMessagePicture(NULL, $message['thread'])
            . "</div><div style='width:100%;display:table-cell;vertical-align:top;'>"
            . "<p class='ellipsis_overflow notification_name'>" . $participants . "</p>"
            . "<p class='ellipsis_overflow notification_info'>" . $message['message']
            . "</p></div></div></a></li> ";
        }
        $total_message_count = count($messages);
        if ($total_message_count == 0) {
            echo "<div style='padding:5px;color:grey;'>No Messages</div>";
        }
    }

    function deleteMessage($thread) {
        $sql = "UPDATE message_share SET visible=0 WHERE `thread` = :thread AND user_id = :user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id,
            ":thread" => $thread
            ));
    }

    function markMessageRead($type = 'thread', $id) {
        if ($type == "thread") {
            $thread = $id;
        }
        $sql = "UPDATE message_share SET seen=1, `read`=1 WHERE `thread` = " . $id . " AND user_id=" . base64_decode($_COOKIE['id']) . ";";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute();
    }

    function markAllMessageSeen() {
        $sql = "UPDATE message_share SET seen=1 WHERE user_id=" . base64_decode($_COOKIE['id']) . ";";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute();
    }

    public function getReceivers($thread, $style = false) {
        $receivers = array();
        
        $sql = "SELECT id FROM user WHERE id IN(SELECT user_id FROM message_share WHERE `thread` = :thread);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
                array(
                    ":thread" => $thread,
        ));
        $receivers['user'] = $sql->fetchAll(PDO::FETCH_COLUMN);
        $receivers['user'] = array_values($receivers['user']);
        $receivers['group'] = array();
        $receivers['community'] = array();
        //echo $thread;
        //var_dump($receivers);
        if ($style !== false) {
            return $this->styleReceiverList($receivers, $style);
        }
        return $receivers;
    }   
    
    public function getMessagePicture($size, $thread) {
        $all_receivers = $this->getReceivers($thread, false);
        foreach ($all_receivers as $key => $receivers) {
            foreach ($receivers as $user_key => $single_id) {
                if($key == "user") {
                    if($single_id == $this->user->user_id) {
                        unset($receivers[$user_key]);
                    }
                }
            }
        }
        $return = '';
        $count = count($all_receivers['user'] + $all_receivers['group'] + $all_receivers['community']);
        
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
        foreach ($all_receivers['user'] as $key => $receiver) {
            if($key < 4) {
                $return .= $this->img($this->user->getProfilePicture('chat', $receiver), $width[$key], $height[$key]);
            }
        }
        return $return;
    }
    function img($src, $width = "'auto'", $height = "'auto'", $styles = NULL) {
        return "<div style='display:inline-block;background-size:cover;background-image: url(\"".$src."\");height:" . $height . "; width:" . $width . "'></div>";
    }

    private function styleReceiverList($receivers, $type) {
        
        if(!array_key_exists('group', $receivers)) {
            $receivers['group'] = array();
        }
        if(!array_key_exists('community', $receivers)) {
            $receivers['community'] = array();
        }
        $return = NULL;
        $num = count($receivers['user'] + $receivers['group'] + $receivers['community']);
        $current_num = 0;

        foreach ($receivers as $key => $receiver) {
            foreach ($receiver as $single_id) {
                if($key == 'user') {
                    if ($this->user->user_id != $single_id) {
                        if ($type == "header") {
                            $return .= "<a href='user?id=" . urlencode(base64_encode($single_id))
                                    . "'><span style='margin-right:5px;' "
                                    . " class='message_convo_receiver user_preview' user_id='" . $single_id . "'>";
                            $return .= $this->user->getName($single_id, 1);
                            if ($num - 1 != $current_num) {
                                $return .= ",";
                            }
                            $return .= "</span></a>";
                        }
                        else if ($type == "list") {
                            $name = $this->user->getName($single_id, 1);
                            if ($num - 1 != $current_num) {
                                $name .= ",";
                            }
                            $return .= "" . $name . "";
                        }
                    }
                }
                $current_num++;
            }
        }
        return $return;
    }
    
    private function format_receivers($receivers) {
        if (!array_key_exists('group', $receivers)) {
            $receivers['group'] = array();
        }
        if (!array_key_exists('community', $receivers)) {
            $receivers['community'] = array();
        }
        foreach ($receivers as $key => $receiver) {
            if ($key == 'user' && !in_array($this->user->user_id, $receivers[$key])) {
                array_push($receivers[$key], $this->user->user_id);
            }
            $receivers[$key] = array_unique($receivers[$key]);
            $receivers[$key] = array_values($receivers[$key]);
            asort($receivers[$key]);
        }
        return $receivers;
    }
    private function format_message($receivers, $thread) {
        $receivers_final = NULL;
        $uid = NULL;
        $thread_final = NULL;
        
        if (!isset($thread)) { // IF COMPOSING NEW MESSAGE
            $thread_final = $this->getThreadNum(json_encode($this->format_receivers($receivers))); //CREATE NEW THREAD FROM THE FORMATTED RECEIVERS
            $receivers_final = $this->format_receivers($receivers); // ONLY FORMAT RECEIVERS, DON'T FETCH FROM DB
        } else { // IF REPLYING TO MESSAGE
            $receivers_final = $this->format_receivers($this->getReceivers($thread)); // FETCH RECEIVERS FROM DB AND FORMAT
            $thread_final = $thread;
        }
        $uid = json_encode($receivers_final);
        
        return array("receivers" => $receivers_final, "uid" => $uid, "thread" => $thread_final);
    }

    public function sendMessage($message, $receivers = null, $thread = null) { 
        $info = $this->format_message($receivers, $thread);
        //var_dump($info);
       
        $this->database_connection->beginTransaction();
        
        foreach ($info['receivers'] as $key => $receiver) {
             foreach ($receiver as $single_id) {
                $this->insertMessageShare($single_id, $key, $info['thread']);
            }
        }
        
        $this->insertMessage($message, $info['thread'], $info['uid']);
        $this->database_connection->commit();
    }

    private function insertMessage($message, $thread, $uid) {
        $message_query = "INSERT INTO message (user_id, message, thread, time, u_id) VALUES (:user_id, :message, :thread_id, :time, :u_id);";
        $message_query = $this->database_connection->prepare($message_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $message_query->execute(
                array(
                    ":user_id" => base64_decode($_COOKIE['id']),
                    ":message" => $message, ":thread_id" => $thread,
                    ":time" => time(),
                    ":u_id" => $uid,
        ));
    }

    private function insertMessageShare($receiver, $key, $thread) {
        $message_query = "INSERT INTO  message_share(".$key."_id, `thread`, `seen`, `read`) VALUES (:receiver_id, :thread_id, :seen, :read)";
        $message_query = $this->database_connection->prepare($message_query);
        $message_query->execute(
                array(
                    ":receiver_id" => $receiver,
                    ":thread_id" => $thread,
                    ":seen" => ($receiver == $this->user->getId() && $key == 'user' ? 1 : 0),
                    ":read" => ($receiver == $this->user->getId() && $key == 'user' ? 1 : 0),
        ));
    }

    private function getThreadNum($uid) {
        $getthreadvalue = "SELECT MAX(thread) FROM message;";
        $getthreadvalue = $this->database_connection->prepare($getthreadvalue);
        $getthreadvalue->execute();
        $thread = $getthreadvalue->fetchColumn() + 1;

        $sql = "SELECT thread FROM message WHERE u_id = :u_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
                array(
                    ":u_id" => $uid,
        ));
        $num = $sql->rowCount();
        if ($num > 0) {
            //echo("Found thread number ".$sql->fetchColumn()." with the UID: ".$uid);
            $thread = $sql->fetchColumn();
        }
        return $thread;
    }

    public function getRecentThread($user_id = NULL) {
        if($user_id === NULL) {
            $sql = "SELECT thread FROM message WHERE thread IN "
                    . "(SELECT `thread` FROM message_share WHERE visible=1 AND user_id = :user_id)"
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

    function getNotificationList() {
        $notify_count = $this->getNotificationNum();
        $notifications = $this->getNotification();
        foreach ($notifications as $notify) {
            $picture = $this->user->getProfilePicture("chat", $notify['user_id']);
            $name = $this->user->getName($notify['user_id']);
            echo "<li onclick='markNotificationRead(" . $notify['id'] . ", \"post?a=" . $notify['post_id'] . "\");' class='";
            if ($notify['read'] == 0) {
                echo "messageunread";
            }
            else {
                echo "message";
            }

            if ($notify['type'] == 'like' || $notify['type'] == 'dislike') {
                echo "'><div style='display:table-row;'>"
                . "<img class='notification_user_image' src='" . $picture . "'></img>"
                . "<p style='vertical-align:top; display:table-cell;'><b>" . $name . "</b> liked on your post</p></div></li> ";
            }
            else if ($notify['type'] == 'comment_like') {
                echo "'><div style='display:table-row;'>"
                . "<img class='notification_user_image' src='" . $picture . "'></img>"
                . "<p style='vertical-align:top; display:table-cell;'><b>" . $name . "</b> liked your comment</p></div></li> ";
            }
        }
        $total_notify_count = count($notifications);
        if ($total_notify_count == 0) {
            echo "<div style='padding:5px;color:grey;'>No Notifications</div>";
        }
    }

    function markAllNotificationsSeen() {
        $this->database_connection->query("UPDATE notification SET seen = 1 WHERE receiver_id = " . base64_decode($_COOKIE['id']) . "");
    }

    function getNotificationNum() {
        $user_query = "SELECT user_id, time FROM connection_invite WHERE receiver_id = :user_id "
        . "UNION SELECT event_id, time FROM event_share WHERE user_id = :user_id ORDER BY time;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return 0;//$user;
    }

    function markNotificationRead($id) {
        $this->database_connection->query("UPDATE notification SET `read`= 1 WHERE id = " . $id . "");
    }

    function getNetwork() {
        $user_query = "SELECT * FROM group_invite WHERE user_id = :user_id ORDER BY time DESC;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->fetchAll();
        return $user;
    }

    function getConnection() {
        $user_query = "SELECT * FROM connection_invite WHERE receiver_id = :user_id ORDER BY time DESC;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user_1 = $user_query->fetchAll();
        return $user_1;
    }
    function getConnectionNum() {
        $user_query = "SELECT id FROM connection_invite WHERE user_id = :user_id AND `seen` = 0 AND invite_status = 1;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return $user;
    }
    function getNetworkNum() {
        $user_query = "SELECT id FROM group_invite WHERE user_id = :user_id AND `seen` = 0 AND invite_status = 1;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => base64_decode($_COOKIE['id'])));
        $user = $user_query->rowCount();
        return $user;
    }

    function getNetworkList() {
        $network_count = $this->getNetworkNum();
        $networks = $this->getNetwork();
        $total_network_count = count($networks);
        if ($total_network_count == 0) {
            echo "<div style='padding:5px;color:grey;'>No Network Notifications</div>";
        }
        foreach ($networks as $network) {
            $picture = $this->user->getProfilePicture("chat", $network['sender_id']);
            $group_name = $this->group->getGroupName($network['group_id']);
            $group_id = $network['group_id'];

            echo "<li class='";
            if ($network['read'] == 0) {
                echo "messageunread";
            }
            else {
                echo "message";
            }
            echo "'><table><tr style='vertical-align:top;'>"
            . "<td style='min-width:40px;'>"
            . "<img class='notification_user_image' src='" . $picture . "'></img>"
            . "</td><td>"
            . "<p style='margin:0;text-align:left;font-size:13px;'>"
            . str_replace('$group', $group_name, str_replace('$user', $this->user->getName($network['sender_id']), $this->phrase->get('group_invite', 'en')))
            . "</p></td><td><table cellspacing='0' cellpadding='0'><tr><td>";
            if ($network['invite_status'] == 2) {
                echo "<button style='margin:0;' onclick='rejectGroup("
                . $group_id . ", " . $network['id'] . ");' "
                . "class='pure-button-yellow small' id='leave_group_" . $network['id'] . "'>Leave</button>";
            }
            else if ($network['invite_status'] == 0) {
                echo "<button style='margin:0;' onclick='joinGroup("
                . $group_id . ", " . $network['id'] . ");' "
                . "class='pure-button-primary small' id='join_button_" . $network['id'] . "'>Join</button>";
            }
            else {
                echo "<button style='margin:0;' onclick='joinGroup("
                . $group_id . ", " . $network['id'] . ");' "
                . "class='pure-button-success small' "
                . "id='join_button_" . $network['id'] . "' >Join</button>"
                . "</td></tr><tr><td><button onclick='rejectGroup(" . $group_id . ", " . $network['id'] . ");' "
                . "class='pure-button-error small' id='reject_button_" . $network['id'] . "'>Reject</button>";
                echo "<button style='margin:0;display:none;' onclick='rejectGroup("
                . $group_id . ", " . $network['id'] . ");' "
                . "class='pure-button-yellow small' id='leave_button_" . $network['id'] . "'>Leave</button>";
            }
            echo "</td></tr></table></td></tr></table></li>";
        }

        $network_count = $this->getConnectionNum();
        $networks = $this->getConnection();
        $total_network_count = count($networks);
        if ($total_network_count == 0) {
            echo "<div style='padding:5px;color:grey;'>No Network Notifications</div>";
        }
        foreach ($networks as $network) {
            $picture = $this->user->getProfilePicture("chat", $network['user_id']);

            echo "<li class='";
            if ($network['read'] == 0) {
                echo "messageunread";
            }
            else {
                echo "message";
            }
            echo "'><table><tr style='vertical-align:top;'>"
            . "<td style='min-width:40px;'>"
            . "<img class='notification_user_image' src='" . $picture . "'></img>"
            . "</td><td>"
            . "<p style='margin:0;text-align:left;font-size:13px;'>"
            . str_replace('$user', $this->user->getName($network['user_id']), $this->phrase->get('connection_invite', 'en'))
            . "</p></td><td><table cellspacing='0' cellpadding='0'><tr><td>";
            if ($network['status'] == 2) {
                echo "<button style='margin:0;' data-invite_id='".$network['id']."' class='pure-button-primary connect_accept'>Connect</button>";
            }
            echo "</td></tr></table></td></tr></table></li>";
        }
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
            $return['network'] = $notify->getNetworkNum() + $notify->getConnectionNum();
            $return['notification'] = $notify->getNotificationNum();
            $return = json_encode($return);
            die($return);
        }
        else if($_POST['action'] === "messageList") {
            die($notify->getMessageList());
        }
        else if($_POST['action'] === "notificationList") {
            die($notify->getNotificationList());
        }
        else if($_POST['action'] === "networkList") {
            die($notify->getNetworkList());
        }
        else if($_POST['action'] == "deleteMessage") {
            die($notify->deleteMessage($_POST['thread']));
        }
        else if($_POST['action'] == "updateMesssage") {
            die($notify->getMessageNew($thread));
        }
        else if($_POST['action'] == "get_thread") {
            $oldest = $newest = NULL;
            if(isset($_POST['min']) && !empty($_POST['min']) && $_POST['min'] != "") {
                $oldest = $_POST['min'];
            }
            if(isset($_POST['max']) && !empty($_POST['max']) && $_POST['max'] != "") {
                $newest = $_POST['max'];
            }
            die(json_encode($notify->getMessage('thread', $_POST['thread'], $oldest, $newest), JSON_HEX_APOS));
        }
    }
}
?>