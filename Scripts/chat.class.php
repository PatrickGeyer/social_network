<?php

include_once('database.class.php');
include_once('user.class.php');

class Chat {
    private static $chat = NULL;
    private $user;
    protected $database_connection;
    public function __construct() {
        $this->user = User::getInstance();
        $this->database_connection = Database::getConnection();
    }
    public static function getInstance ( ) {
        if (self :: $chat) {
            return self :: $chat;
        }

        self :: $chat = new Chat();
        return self :: $chat;
    }
    public function submitChat($aimed, $text) {
        
        $text = strip_tags($text);
        $text = nl2br($text);
        if ($text == "") {
            
        } else if ($aimed == "s" || $aimed == "y") {
            $sql = "INSERT INTO chat(sender_id, `text`, community_id, sender_year, aimed, time) VALUES(:user_id, :text, :user_community, :user_year, :aimed, :time);";
            $variables = array(
                ":user_id" => $this->user->getId(),
                ":user_community" => $this->user->getCommunityId(),
                ":user_year" => $this->user->getPosition(),
                ":text" => $text,
                ":time" => time(),
                ":aimed" => $aimed,
            );
        } else {
            $sql = "INSERT INTO chat(sender_id, `text`, group_id, time) VALUES(:user_id, :text, :aimed, :time);";
            $variables = array(
                ":user_id" => $this->user->getId(),
                ":text" => $text,
                ":time" => time(),
                ":aimed" => $aimed,
            );
        }
        $sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sql->execute($variables);
        echo "200";
    }

    function getContent($chat_identifier, $all = 'false') {
        $time = time();
        if ($chat_identifier == "y") {
            $chat_query = "SELECT sender_id, `text`, time, id FROM chat WHERE community_id = " . $this->user->getCommunityId() . " AND sender_year = " . $this->user->getPosition() . " AND aimed = 'y' ";
        } else if ($chat_identifier == "s") {
            $chat_query = "SELECT sender_id, `text`, time, id FROM chat WHERE community_id = " . $this->user->getCommunityId() . " AND aimed='s'";
        } else {
            $chat_query = "SELECT sender_id, `text`, time, id FROM chat WHERE group_id = " . $chat_identifier;
        }
        if ($all == 'false') {
            $chat_query .= " AND time >= " . $time;
        }
        $chat_query .= ";";
        $chat_query = $this->database_connection->prepare($chat_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        if ($all == 'false') {
            while ((time() - $time) < 30) {
                $chat_query->execute();
                $chat_number = $chat_query->rowCount();
                $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                if ($chat_number == 0) {
                    usleep(500000);
                } else {
                    foreach ($chat_entries as $record) {
                        $chat_read_query = "SELECT id FROM chat_read WHERE user_id = :user_id AND chat_id = :chat_id;";
                        $chat_read_query = $this->database_connection->prepare($chat_read_query);
                        $chat_read_query->execute(
                                array(
                                    ":user_id" => $this->user->user_id,
                                    ":chat_id" => $record['id'],
                        ));
                        $num = $chat_read_query->rowCount();
                        if ($num == 0) {
                            $this->chatify($record);
                            $this->markChatRead($record['id']);
                        }
                    }
                    break;
                }
            }
        } else {
            $chat_query->execute();
            $chat_number = $chat_query->rowCount();
            $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
            if ($chat_number != 0) {
                foreach ($chat_entries as $record) {
                    $this->chatify($record);
                }
            } else {
                echo "No chat entries here";
            }
        }
    }

    private function markChatRead($id = NULL) {
        if ($id != NULL) {
            $sql = "INSERT INTO chat_read (user_id, chat_id) VALUES (:user_id, :chat_id);";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(
                    array(
                        ":user_id" => $this->user->getId(),
                        ":chat_id" => $id,
            ));
        }
    }

    private function chatify($record) {
        $online = $this->user->getOnline($record['sender_id']);
        echo "<li class='single_chat '>";
        echo "<div class='";
        if ($record['sender_id'] != $this->user->getId()) {
            echo "chat_wrapper";
        } else {
            echo "chat_my_wrapper";
        }
        echo "'>";
        echo "<table cellspacing='0' cellpadding='0' style='width:100%;'><tr>";//<td style='width:50px;padding-right:5px;'>";
        //echo "<div class='chat_user_profile' style='border-left:2px solid ".($this->user->getOnline($record['sender_id']) == true ? "rgb(28, 184, 65)" : "red")."; float:left;width:40px;height:40px;background-image:url(" . 
                //$this->user->getProfilePicture('chat', $record['sender_id']) . ");background-size:cover;'></div>";
        echo "<td>"; //</td>
        echo "<div class='chatname'><span class='user_preview user_preview_name chatname' style='font-size:13px;' user_id='"
            .$record['sender_id']."'>" . $this->user->getName($record['sender_id']) . "</span></div>";
        echo "<div class='chattext'>";
        echo $record['text'];
        echo "</div>";
        echo "</td></tr></table></div>";
        echo "</li>";
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $chat = Chat::getInstance();
    if (isset($_POST['chat'])) {
        $chat->getContent($_POST['chat'], $_POST['all']);
    }
    if (isset($_POST['action']) && $_POST['action'] == "addchat") {
        $chat->submitChat($_POST['aimed'], $_POST['chat_text']);
    }
}
?>