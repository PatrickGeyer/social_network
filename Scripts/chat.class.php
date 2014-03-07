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

    function getContent($chat_identifier, $all = 'false', $min = 0, $max = 999999999999) {
        $time = time();
        if ($chat_identifier == "y") {
            $chat_query_string = "SELECT user_id, `text`, time, id FROM chat WHERE community_id = " . $this->user->getCommunityId() . " AND sender_year = " . $this->user->getPosition() . " AND aimed = 'y' ";
        } else if ($chat_identifier == "s") {
            $chat_query_string = "SELECT user_id, `text`, time, id FROM chat WHERE community_id = " . $this->user->getCommunityId() . " AND aimed='s'";
        } else {
            $chat_query_string = "SELECT user_id, `text`, time, id FROM chat WHERE group_id = " . $chat_identifier;
        }
        if ($all == 'false') {
            $chat_query_string .= "AND time >= " . $time;
        }
        if($all == "previous") {
            $chat_query_string .= " AND (id BETWEEN :min AND :max)";
        }
        $chat_query_string1 = $chat_query_string . " ORDER BY time ";
        $chat_query = $this->database_connection->prepare($chat_query_string1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        $chat_array = array();
        $chat_ids = array();

        if ($all == 'false') {
            while ((time() - $time) < 30) {
                $chat_query->execute(array(":min" => $min, ":max" => $max));
                $chat_number = $chat_query->rowCount();
                if ($chat_number == 0) {
                    usleep(500000);
                } else {
                    $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($chat_entries as $record) {
                        array_push($chat_ids, $record['id']);
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
                            $record['pic'] = $this->user->getProfilePicture('chat', $record['user_id']);
                            $record['time'] = $this->system->humanTiming($record['time']);
                            $record['name'] = $this->user->getName($record['user_id']);
                            $chat_array[] = $record;
                            $this->markChatRead($record['id']);
                        }
                    }
                    break;
                }
            }
        } else {
            $chat_query->execute(array(":min" => $min, ":max" => $max));
            $chat_number = $chat_query->rowCount();
            if ($chat_number != 0) {
                $chat_entries = $chat_query->fetchAll(PDO::FETCH_ASSOC);
                $i = count($chat_entries);
                foreach ($chat_entries as $record) {
                    if($i < 20) {
                        array_push($chat_ids, $record['id']);
                        $record['pic'] = $this->user->getProfilePicture('chat', $record['user_id']);
                        $record['time'] = $this->system->humanTiming($record['time']);
                        $record['name'] = $this->user->getName($record['user_id']);
                        $chat_array[] = $record;
                        $this->markChatRead($record['id']);
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
        	$chat_min->execute(array(":min" => $min, ":max" => $max));
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