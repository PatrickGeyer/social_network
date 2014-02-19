<?php

include_once('database.class.php');
include_once('user.class.php');
include_once('system.class.php');

class Chat {
    private static $chat = NULL;
    private $user;
    protected $database_connection;
    public function __construct() {
        $this->user = User::getInstance();
        $this->database_connection = Database::getConnection();
        $this->system = System::getInstance();
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
        $text = trim($text);
        $text = $this->system->linkReplace($text, NULL, "[", "]");
        $text = preg_replace('/\n(\s*\n){2,}/', "<br>", $text);
        //$text = preg_replace('/<br>(\s*\n){2,}/', "<br>", $text);
        if ($text == "" || $text == "<br>") {
            
        } else if ($aimed == "s" || $aimed == "y") {
            $sql = "INSERT INTO chat(user_id, `text`, community_id, sender_year, aimed, time) VALUES(:user_id, :text, :user_community, :user_year, :aimed, :time);";
            $variables = array(
                ":user_id" => $this->user->getId(),
                ":user_community" => $this->user->getCommunityId(),
                ":user_year" => $this->user->getPosition(),
                ":text" => $text,
                ":time" => time(),
                ":aimed" => $aimed,
            );
        } else {
            $sql = "INSERT INTO chat(user_id, `text`, group_id, time) VALUES(:user_id, :text, :aimed, :time);";
            $variables = array(
                ":user_id" => $this->user->getId(),
                ":text" => $text,
                ":time" => time(),
                ":aimed" => $aimed,
            );
        }
        $sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sql->execute($variables);
    }

    function getContent($chat_identifier, $all = 'false') {
        $time = time();
        if ($chat_identifier == "y") {
            $chat_query = "SELECT user_id, `text`, time, id FROM chat WHERE community_id = " . $this->user->getCommunityId() . " AND sender_year = " . $this->user->getPosition() . " AND aimed = 'y' ";
        } else if ($chat_identifier == "s") {
            $chat_query = "SELECT user_id, `text`, time, id FROM chat WHERE community_id = " . $this->user->getCommunityId() . " AND aimed='s'";
        } else {
            $chat_query = "SELECT user_id, `text`, time, id FROM chat WHERE group_id = " . $chat_identifier;
        }
        if ($all == 'false') {
            $chat_query .= "AND time >= " . $time;
        }
        //$chat_query .= " AND id NOT IN(SELECT )"
        $chat_query .= " ORDER BY time;";
        $chat_query = $this->database_connection->prepare($chat_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        if ($all == 'false') {
            while ((time() - $time) < 30) {
                $chat_query->execute();
                $chat_number = $chat_query->rowCount();
                if ($chat_number == 0) {
                    usleep(500000);
                } else {
                    $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($chat_entries as $record) {
                        $chat_read_query = "SELECT id, chat_id FROM chat_read WHERE user_id = :user_id AND chat_id = :chat_id;";
                        $chat_read_query = $this->database_connection->prepare($chat_read_query);
                        $chat_read_query->execute(
                                array(
                                    ":user_id" => $this->user->user_id,
                                    ":chat_id" => $record['id'],
                        ));
                        $num = $chat_read_query->rowCount();
                        $num1 = $chat_read_query->fetch(PDO::FETCH_ASSOC);
                        if ($num === 0) {
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
            if ($chat_number != 0) {
                $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($chat_entries as $record) {
                    $this->chatify($record);
                    $this->markChatRead($record['id']);
                }
            } else {
                echo "No chat entries here";
            }
        }
    }

    private function markChatRead($id = NULL) {
        if ($id != NULL) {
            $sql = "DELETE FROM chat_read WHERE user_id = :user_id AND chat_id = :chat_id;"
                    . " INSERT INTO chat_read (user_id, chat_id) VALUES (:user_id, :chat_id);";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(
                    array(
                        ":user_id" => $this->user->getId(),
                        ":chat_id" => $id,
            ));
        }
    }

    private function chatify($record) {
        $online = $this->user->getOnline($record['user_id']);
        echo "<li class='single_chat '>";
        echo "<div class='";
        if ($record['sender_id'] != $this->user->getId()) {
            echo "chat_wrapper";
        } else {
            echo "chat_my_wrapper";
        }
        echo "'>";
        echo "<table cellspacing='0' cellpadding='0' style='width:100%;'><tr>";
        echo "<td style='width:50px;padding-right:5px;'>";
        echo "<div class='profile_picture_medium profile_picture_".$record['user_id']."' style='border-left:2px solid "
        .($this->user->getOnline($record['user_id']) == true || $record['user_id'] == $this->user->user_id ? "turquoise" : "grey")."; float:left;width:40px;height:40px;background-image:url(" . 
                $this->user->getProfilePicture('chat', $record['user_id']) . ");background-size:cover;'></div>";
        echo "</td><td>";
        echo "<div class='chatname'><span class='user_preview user_preview_name chatname' style='margin-right:5px;font-size:13px;' user_id='"
            . $record['user_id']."'>" . $this->user->getName($record['user_id']) . "</span>"
            . "</div>";
        echo "<div class='chattext'>";
        echo $record['text'];
        echo "</div>";
        
        echo "</td></tr><tr><td colspan='2' style='text-align:right;'>"
            . "<span class='chat_time post_comment_time'>".$this->system->humanTiming($record['time'])."</span>"
            . "</td></tr></table></div>";
        echo "</li>";
    }

    function getUnreadNum($aimed_id = '', $aimed, $position) {
        $sql = 'SELECT id FROM chat WHERE '.$aimed.'_id = :aimed_id AND ';
        if(isset($position)) {
           // $sql .= "aimed = :position AND ";
        }
        $sql .= 'id NOT IN (SELECT chat_id FROM chat_read WHERE user_id = :user_id);';
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":aimed_id" => $aimed_id,
            ":user_id" => $this->user->user_id
            ));
        return $sql->rowCount();
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