<?php
include_once('database.class.php');

class User {

    public $user_id;
    private $database_connection;
    private static $user = null ;
    public function __construct() {
        if(isset($_COOKIE['id'])) {
            $this->user_id = base64_decode($_COOKIE['id']);
            $this->database_connection = Database::getConnection();
        }
    }
    public static function getInstance ( ) {
        if (self :: $user) {
            return self :: $user;
        }

        self :: $user = new User;
        return self :: $user;
    }
    function getId() {
        return $this->user_id;
    }

    function getName($id = null, $name = 3) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        if($name === 3) {
            $user_query = "SELECT name FROM users WHERE id = :user_id";
        }
        else if($name === 1) {
            $user_query = "SELECT first_name FROM users WHERE id = :user_id";
        }
        else if($name === 2) {
            $user_query = "SELECT last_name FROM users WHERE id = :user_id";
        }
        //print_r($this);
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getPosition($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT position FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }
    
    function getLocation($id = NULL) {
        if(!isset($id)) {
            $id = $this->user_id;
        }
        $location_query = "SELECT id, country, region, city, lat, lng, type, name, time FROM marker WHERE user_id = :user_id ORDER BY time LIMIT 1;";
        $location_query = $this->database_connection->prepare($location_query);
        $location_query->execute(array(":user_id" => $id));
        $location = $location_query->fetch(PDO::FETCH_ASSOC);
        return $location;
    }
    function setLocation($id = NULL) {
        if(!isset($id)) {
            $id = $this->user_id;
        } 
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException("IP invalid");
        }
        $ipLite = new ip2location_lite;
        $ipLite->setKey('b9afa6a06d06ac61e4879994d40bbd76af1137438cd1ca2d57a6334345a70f33');
        $locations = $ipLite->getCity($ip);
        
        $sql = "INSERT INTO marker (user_id, country, region, city, lat, lng, time) VALUES (:user_id, :country, :region, :city, :lat, :lng, :time);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(
                array(
                    ":user_id" => $id,
                    ":country" => $locations['countryName'],
                    ":region" => $locations['regionName'],
                    ":lat" => $locations['latitude'],
                    ":lng" => $locations['longitude'],
                    ":city" => $locations['cityName'],
                    ":time" => time(),
                ));
    }

    function getCommunityId($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT community_id FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getCommunityName($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT name FROM community WHERE id = (SELECT community_id FROM users WHERE id = :user_id);";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getProfilePicture($size = "thumb", $id = null) {
        if ($id == null) {
            $id = $this->user_id;
        }
        if ($size == "original") {
            $user_query = "SELECT path FROM files WHERE id = (SELECT profile_picture FROM users WHERE id = :user_id);";
        } else if ($size == "thumb" || $size == "chat") {
            $user_query = "SELECT thumb_path FROM files WHERE id = (SELECT profile_picture FROM users WHERE id = :user_id);";
        } else if ($size == "icon") {
            $user_query = "SELECT icon_path FROM files WHERE id = (SELECT profile_picture FROM users WHERE id = :user_id);";
        }
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user_profile_picture = $user_query->fetchColumn();
        if (!empty($user_profile_picture) || $user_profile_picture != "") {
            //die("PROFILE PATH: ".$user_profile_picture);
            return $user_profile_picture;
        } else {
            if ($this->getGender($id) == "Male") {
                return Base::MALE_DEFAULT_ICON;
            } else {
                return Base::FEMALE_DEFAULT_ICON;
            }
        }
    }

    function getGender($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT gender FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getAbout($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT about FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if(strtolower($user) == "null" || $user == "") {
            $user = "";
        }
        return $user;
    }

    function getLanguage($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT default_language FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function getEmail($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT email FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function isAdmin($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT admin FROM users WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function updateSettings($about = null, $community = null, $year = null, $email = null, $language = null) {
        $sql = "UPDATE users SET ";
        if (isset($about)) {
            $sql .= 'about="' . $about . '"';
        }
        if (isset($community) && $community != "") {
            $sql .= ',school = "' . $community . '"';
        }
        if (isset($year) && $community != "") {
            $sql .= ', year = "' . $year . '" ';
        }
        if (isset($email) && $community != "") {
            $sql .= ',email="' . $email . '" ';
        }
        if (isset($language) && $community != "") {
            $sql .= ',default_language="' . $language . '"';
        }
        $sql .= ' WHERE id=' . $this->getId();
        $sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sql->execute(array());
    }

    function getBookmarks($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT id, name, link FROM bookmark WHERE user_id = :user_id";
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetch();
        return $user;
    }
    /**
     * function notify (Send a notification to a User)
     * @param string $type
     * @param int $receiver_id
     * @param int $activity_id
     * @param int $read
     * @param int $seen
     */
    public function notify($type, $receiver_id, $activity_id, $element = NULL, $read = 0, $seen = 0) {
        $who_liked_query = "INSERT INTO notification (`type`, post_id, receiver_id, sender_id, `read`, seen) "
                . "VALUES(:type, :activity_id, :receiver_id, :sender_id, :read, :seen);";
        $who_liked_query = $this->database_connection->prepare($who_liked_query);
        $who_liked_query->execute(array(
            ":activity_id" => $activity_id,
            ":receiver_id" => $receiver_id,
            ":sender_id" => $this->user_id,
            ":type" => $type,
            ":read" => $read,
            ":seen" => $seen
        ));
    }

    function setOnline($id = null) {
        if ($id == null) {
            $id = $this->user_id;
        }
        $this->database_connection->prepare("UPDATE users SET online = 1 WHERE id = " . $id . ";")->execute();
        $this->database_connection->prepare("UPDATE users SET lastactivity = NOW() WHERE id = " . $id . ";")->execute();
    }

    function getOnline($id) {
        if ($id == null) {
            $id = $this->user_id;
        }
        $sql = "SELECT id FROM users WHERE id = " . $id . " AND lastactivity > DATE_SUB(NOW(), INTERVAL 30 SECOND);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute();
        $num = $sql->rowCount();

        if ($num == 0) {
            return false;
        } else {
            return true;
        }
    }
    
    function setProfilePicture($id) {
        $sql = "UPDATE users SET profile_picture = :id WHERE id = :user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":id" => $id,
            ":user_id" => $this->user_id
        ));
        die("UPDATED");
    }

}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = new User();
    if(isset($_POST['about'])) {
        if (!isset($_POST['email'])) {
            $email = $user->getEmail();
        } else {
            $email = $_POST['email'];
        }
        $user->updateSettings($_POST['about'], $email, $_POST['year']);
    }
    
    if(isset($_POST['action'])) {
        if($_POST['action'] == 'get_preview_info') {
            $array[] = urlencode(base64_encode($_POST['id']));
            $array[] = $user->getName($_POST['id']);
            $array[] = $user->getProfilePicture('chat', $_POST['id']);
            $array[] = $user->getAbout($_POST['id']);
            $array[] = $user->getCommunityName($_POST['id']);
            $array[] = $user->getPosition($_POST['id']);
            $array[] = urlencode(base64_encode($user->getCommunityId($_POST['id'])));
            echo json_encode($array);
        }     
        if($_POST['action'] == 'setOnline')
        {
            $user->setOnline();
        }   
        if($_POST['action'] == "profile_picture") {
            $user->setProfilePicture($_POST['file_id']);
        }
    }
}
?>