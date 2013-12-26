<?php

include_once('database.class.php');

class User extends Database {

    public $user_id;

    public function __construct() {
        parent::__construct();
        $this->user_id = base64_decode($_COOKIE['id']);
        return true;
    }

    function getId() {
        return $this->user_id;
    }

    function getName($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT name FROM users WHERE id = :user_id";
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
        $location_query = "SELECT * FROM marker WHERE user_id = :user_id ORDER BY time LIMIT 1;";
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

    function getProfilePicture($size = "chat", $id = null) {
        if ($id == null) {
            $id = $this->user_id;
        }
        if ($size == "original") {
            $user_query = "SELECT profile_picture FROM users WHERE id = :user_id";
        } else if ($size == "thumb") {
            $user_query = "SELECT profile_picture_thumb FROM users WHERE id = :user_id";
        } else if ($size == "icon") {
            $user_query = "SELECT profile_picture_icon FROM users WHERE id = :user_id";
        } else if ($size == "chat") {
            $user_query = "SELECT profile_picture_chat_icon FROM users WHERE id = :user_id";
        } else {
            $user_query = "SELECT profile_picture_chat_icon FROM users WHERE id = :user_id";
        }
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user_profile_picture = $user_query->fetchColumn();
        if (!empty($user_profile_picture) || $user_profile_picture != "") {
            return $user_profile_picture;
        } else {
            if ($this->getGender($id) == "Male") {
                return "Images/profile-picture-default-male-" . $size . ".jpg";
            } else {
                return "Images/profile-picture-default-female-" . $size . ".jpg";
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
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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

    function getActivity($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }

        $activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
		community_id = :community_id
		OR (year = :user_year AND community_id = :community_id) 
		OR group_id in (SELECT group_id FROM group_member WHERE member_id = :user_id) 
		OR receiver_id = :user_id) AND user_id = " . $id . "
		ORDER BY time DESC";
        $activity_query = $this->database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":user_id" => $id, ":community_id" => $this->getCommunityId($id), ":user_year" => $this->getPosition($id)));
        $return = $activity_query->fetchAll(PDO::FETCH_ASSOC);
        return $return;
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
        $user_query = "SELECT * FROM bookmark WHERE user_id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetch();
        return $user;
    }

    function setOnline($id = null) {
        if ($id == null) {
            $id = $this->user_id;
        }
        $this->database_connection->query("UPDATE users SET online = 1 WHERE id = " . $id . ";");
        $this->database_connection->query("UPDATE users SET lastactivity = NOW() WHERE id = " . $id . ";");
    }

    function getOnline($id) {
        if ($id == null) {
            $id = $this->user_id;
        }
        $sql = "SELECT id FROM users WHERE id = " . $id . " AND lastactivity > DATE_SUB(NOW(), INTERVAL 30 SECOND);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute();
        $online = $sql->fetchColumn();
        $num = $sql->rowCount();

        if ($num == 0) {
            return false;
        } else {
            return true;
        }
    }

}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = new User;
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
    }
}
?>