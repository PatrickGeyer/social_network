<?php
include_once('database.class.php');

class User {
    public $user_id;
    private $password = NULL;
    private $email = NULL;
    private $name = array(1 => NULL, 2 => NULL, 3 => NULL);
    private $community_id = NULL;
    private $community_name = NULL;
    private $position = NULL;
    private $location = NULL;
    private $gender = NULL;
    private $about = NULL;
    private $language = NULL;
    private $profile_picture = array("original" => NULL, "chat"=> NULL, "thumb" => NULL, "icon" => NULL);
    
    private $database_connection;
    private static $user = null;
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
        $set = false;
        if (!isset($id)) {
            $set = true;
            if(!is_null($this->name[$name]))
                return $this->name[$name];
            $id = $this->user_id;
        }
        if($name === 3) {
            $user_query = "SELECT name FROM user WHERE id = :user_id";
        }
        else if($name === 1) {
            $user_query = "SELECT first_name FROM user WHERE id = :user_id";
        }
        else if($name === 2) {
            $user_query = "SELECT last_name FROM user WHERE id = :user_id";
        }
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->name[$name] = $user;
        }
        return $user;
    }

    function getPosition($id = null) {
        $set = false;
        if (!isset($id)) {
            $set = true;
            $id = $this->user_id;
            if(!is_null($this->position)) {
                return $this->position;
            }
        }
        $user_query = "SELECT position FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->position = $user;
        }
        return $user;
    }
    
    function getLocation($id = NULL) {
        $set = false;
        if(!isset($id)) {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->location)) {
                return $this->location;
            }
        }
        $location_query = "SELECT id, country, region, city, lat, lng, type, name, time FROM marker WHERE user_id = :user_id ORDER BY time LIMIT 1;";
        $location_query = $this->database_connection->prepare($location_query);
        $location_query->execute(array(":user_id" => $id));
        $location = $location_query->fetch(PDO::FETCH_ASSOC);
        if($set) {
            $this->community_name = $user;
        }
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
        $set = false;
        if (!isset($id)) {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->community_id)) {
                return $this->community_id;
            }
        }
        $user_query = "SELECT community_id FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->community_id = $user;
        }
        return $user;
    }

    function getCommunityName($id = null) {
        $set = false;
        if (!isset($id)) {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->community_name)) {
                return $this->community_name;
            }
        }
        $user_query = "SELECT name FROM community WHERE id = (SELECT community_id FROM user WHERE id = :user_id);";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->community_name = $user;
        }
        return $user;
    }

    function getProfilePicture($size = "thumb", $id = null) {
        $set = false;
        if ($id == null) {
            $set = true;
            if(!is_null($this->profile_picture["$size"])) {
                return $this->profile_picture["$size"];
            }
            $id = $this->user_id;
        }
        if ($size == "original") {
            $user_query = "SELECT path FROM file WHERE id = (SELECT profile_picture FROM user WHERE id = :user_id);";
        } else if ($size == "thumb" || $size == "chat") {
            $user_query = "SELECT thumb_path FROM file WHERE id = (SELECT profile_picture FROM user WHERE id = :user_id);";
        } else if ($size == "icon") {
            $user_query = "SELECT icon_path FROM file WHERE id = (SELECT profile_picture FROM user WHERE id = :user_id);";
        }
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user_profile_picture = $user_query->fetchColumn();
        $user = NULL;
        if (!empty($user_profile_picture) || $user_profile_picture != "") {
            $user =  $user_profile_picture;
        } else {
            if ($this->getGender($id) == "Male") {
                $user = Base::MALE_DEFAULT_ICON;
            } else {
                $user = Base::FEMALE_DEFAULT_ICON;
            }
        }
        if($set) {
            $this->profile_picture["$size"] = $user;
        }
        return $user;
    }

    function getGender($id = null) {
        $set = false;
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->gender)) {
                return $this->gender;
            }
        }
        $user_query = "SELECT gender FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->gender = $user;
        }
        return $user;
    }

    function getAbout($id = null) {
        $set = false;
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->about)) {
                return $this->about;
            }
        }
        $user_query = "SELECT about FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query);
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if(strtolower($user) == "null" || $user == "") {
            $user = NULL;
        }
        if($set) {
            $this->about = $user;
        }
        return $user;
    }

    function getLanguage($id = null) {
        $set = false;
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->language)) {
                return $this->language;
            }
        }
        $user_query = "SELECT default_language FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->language = $user;
        }
        return $user;
    }

    function getEmail($id = null) {
        $set = false;
        if (!isset($id)) {
            $id = $this->user_id;
            $set = true;
            if(!is_null($this->email)) {
                return $this->email;
            }
        }
        $user_query = "SELECT email FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->email = $user;
        }
        return $user;
    }
    function getPassword($id = NULL) {
        $set = false;
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
            $set = true;
            if(is_null($this->password)) {
                return $this->password;
            }
        }
        $user_query = "SELECT password FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            return $set;
        }
        return $user;
    }
    function isAdmin($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT admin FROM user WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function updateSettings($about = null, $community = null, $year = null, $email = null, $language = null) {
        $sql = "UPDATE user SET ";
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
        //$this->database_connection->prepare("UPDATE user SET online = 1 WHERE id = " . $id . ";")->execute();
        $this->database_connection->prepare("UPDATE user SET last_activity = NOW() WHERE id = " . $id . ";")->execute();
    }

    function getOnline($id) {
        if ($id == null) {
            $id = $this->user_id;
        }
        $id = (int)$id;
        if(is_int($id)) {
            $sql = "SELECT id FROM user WHERE id = " . $id . " AND last_activity > DATE_SUB(NOW(), INTERVAL 1 MINUTE);";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute();
            $num = $sql->rowCount();

            if ($num == 0) {
                return false;
            } else {
                return true;
            }
        }
    }
    
    function setProfilePicture($id) {
        $sql = "UPDATE user SET profile_picture = :id WHERE id = :user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":id" => $id,
            ":user_id" => $this->user_id
        ));
    }

    function printTag($id) {
        $return = "<table cellspacing='0'>"
                . "<tr><td><a href='user?id=".base64_encode($id)."'><div class='profile_picture_medium' style='background-image: url(\"".$this->getProfilePicture("thumb", $id). "\");'></div></a>
                                        </td>
                                        <td>
                                            <p style='padding-left: 0px;' class='settings'>
                                                <a href='user?id=".base64_encode($id)."'>
                                                    <span class='user_preview_name'>". $this->getName($id) ."</span>
                                                </a>
                                            </p>
                                        </td></tr></table>";
        return $return;
                                            
    }
    
    function getOnlineMass($users) {
        $array = array();
        foreach ($users as $user_id) {
            $array[$user_id] = $this->getOnline($user_id);
        }
        return $array;
    }

}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = new User();
    if (isset($_POST['about'])) {
        if (!isset($_POST['email'])) {
            $email = $user->getEmail();
        }
        else {
            $email = $_POST['email'];
        }
        $user->updateSettings($_POST['about'], $email, $_POST['year']);
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'get_preview_info') {
            $array[] = urlencode(base64_encode($_POST['id']));
            $array[] = $user->getName($_POST['id']);
            $array[] = $user->getProfilePicture('chat', $_POST['id']);
            $array[] = $user->getAbout($_POST['id']);
            $array[] = $user->getCommunityName($_POST['id']);
            $array[] = $user->getPosition($_POST['id']);
            $array[] = urlencode(base64_encode($user->getCommunityId($_POST['id'])));
            echo json_encode($array);
        }
        if ($_POST['action'] == 'setOnline') {
            $user->setOnline();
        }
        if($_POST['action'] == "getOnlineMass") {
            die(json_encode($user->getOnlineMass($_POST['users'])));
        }
        if ($_POST['action'] == "profile_picture") {
            $user->setProfilePicture($_POST['file_id']);
        }
    }
}
?>