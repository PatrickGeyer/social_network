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
    public function get_chat_rooms() {
        $sql = "SELECT name, id FROM chat_room WHERE id IN( SELECT chat_room FROM chat_member WHERE user_id = :user_id OR community_id = :community_id OR group_id IN "
                . "(SELECT group_id FROM group_member WHERE user_id = :user_id));";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
             ":user_id" => $this->user->user_id,
             ":community_id" => $this->user->getCommunityId(),
        ));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    public function submitChat($aimed, $text) {
        
        $text = strip_tags($text);
        $text = trim($text);
        $text = $this->system->linkReplace($text, NULL, "[", "]");
        $text = preg_replace('/\n(\s*\n){2,}/', "<br>", $text);
        if ($text == "" || $text == "<br>") {
            
        } else {
            $sql = "INSERT INTO chat(user_id, `text`, chat_room, time) VALUES(:user_id, :text, :aimed, :time);";
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

    function getContent($chat_id, $all = 'false', $min = 0, $max = 999999999999) {
        $time = time();
        
        $chat_query_string = "SELECT chat.user_id, chat.text, chat.time, chat.id FROM "
                . "(SELECT * FROM chat WHERE chat_room = :chat_room ".($all != "false" ? "AND id BETWEEN :min AND :max" : ""). " ORDER BY id DESC LIMIT 25)chat ";
        if ($all == 'false') {
            $chat_query_string .= "WHERE id > :max ORDER BY id ASC;";
            $options = array(
                    ":max" => $max,
                    ":chat_room" => $chat_id,
                        );
        } else if($all == "previous") {
            $chat_query_string .= "WHERE (id BETWEEN :min AND :max) ORDER BY id ASC;";
            $options = array(
                    ":min" => $min, 
                    ":max" => $max,
                    ":chat_room" => $chat_id,
                        );
        } else {
            $chat_query_string .= " ORDER BY id ASC LIMIT 25;";
            $options = array(
                    ":chat_room" => $chat_id,
                    ":min" => $min, 
                    ":max" => $max,
                        );
        }        
        
        $chat_query = $this->database_connection->prepare($chat_query_string);

        $chat_array = array();
        $chat_ids = array();

        if ($all == 'false') {
            while ((time() - $time) < 30) {
                $chat_query->execute($options);
                $chat_number = $chat_query->rowCount();
                if ($chat_number == 0) {
                    usleep(1000000);
                } else {
                    $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($chat_entries as $record) {
                        array_push($chat_ids, $record['id']);
                        $record['pic'] = $this->user->getProfilePicture('chat', $record['user_id']);
                        $record['time'] = $this->system->format_dates($record['time']);
                        $record['name'] = $this->user->getName($record['user_id']);
                        $chat_array[] = $record;
                        //$this->markChatRead($record['id']);
                    }
                    break;
                }
            }
        } else {
            $chat_query->execute($options);
            $chat_number = $chat_query->rowCount();
            if ($chat_number > 0) {
                $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                $i = count($chat_entries);
                foreach ($chat_entries as $record) {
                    if($i < 20) {
                        array_push($chat_ids, $record['id']);
                        $record['pic'] = $this->user->getProfilePicture('chat', $record['user_id']);
                        $record['time'] = $this->system->format_dates($record['time']);
                        $record['name'] = $this->user->getName($record['user_id']);
                        $chat_array[] = $record;
                        //$this->markChatRead($record['id']);
                    }
                    $i--;
                }
            }
        }
        if(is_array($chat_ids)) {
            $min = 0;
            if(count($chat_ids) > 0) {
                $min = min($chat_ids);
            }
            $chat_query_string .= " AND id < ".$min;
        } else {
            $chat_query_string .= " AND id < 1";
        }
        
        if($all == 'previous') {
        	$chat_min = $this->database_connection->prepare($chat_query_string);
        	$chat_min->execute($options);
        	$chat_min = $chat_min->rowCount();
        	if($chat_min == 0) {
          	  	//$chat_array[] = array("type" => 'event', 'code' => 0);
        	}
        }
        return json_encode(array_reverse($chat_array));
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

    function getUnreadNum($chat_room = '') {
        $sql = 'SELECT id FROM chat WHERE chat_room = :aimed_id AND ';
        if(isset($position)) {
           // $sql .= "aimed = :position AND ";
        }
        $sql .= 'id NOT IN (SELECT chat_id FROM chat_read WHERE user_id = :user_id);';
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":aimed_id" => $chat_room,
            ":user_id" => $this->user->user_id
            ));
        return $sql->rowCount();
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $chat = Chat::getInstance();
    if (isset($_POST['chat'])) {
        $oldest = 0;
        $newest = 9999999999999;
        if(isset($_POST['oldest'])) {
            $min = $_POST['oldest'];
        }
        if(isset($_POST['newest'])) {
            $newest = $_POST['newest'];
        }
        die($chat->getContent($_POST['chat'], $_POST['all'], $oldest, $newest));
    }
    if (isset($_POST['action']) && $_POST['action'] == "addchat") {
        $chat->submitChat($_POST['aimed'], $_POST['chat_text']);
    }
}
?>