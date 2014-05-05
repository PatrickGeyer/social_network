<?php
include_once('database.class.php');
include_once('system.class.php');

class User {
    public $user_id;
    private $password = NULL;
    public $email = NULL;
    public $name = array(1 => NULL, 2 => NULL, 3 => NULL);
    public $position = NULL;
    public $location = NULL;
    public $gender = NULL;
    public $about = NULL;
    public $language = NULL;
    public $profile_picture = array("original" => NULL, "chat"=> NULL, "thumb" => NULL, "icon" => NULL);
    public $attr = array();
    
    private static $user = null;
    public function __construct() {
       $this->user_id = (isset($_COOKIE['id']) ? base64_decode($_COOKIE['id']) : "''");
    }
    public static function getInstance ( ) {
        if (self :: $user) {
            return self :: $user;
        }

        self :: $user = new User;
        return self :: $user;
    }

    function create($user_info) {
        $user_info = array(
            'password' => (isset($user_info['password']) ? $user_info['password'] : 'social'),
            'firstname' => (isset($user_info['firstname']) ? $user_info['firstname'] : null),
            'lastname' => (isset($user_info['lastname']) ? $user_info['lastname'] : null),
            'gender' => (isset($user_info['gender']) ? $user_info['gender'] : null),
            'email' => (isset($user_info['email']) ? $user_info['email'] : null),
            'position' => (isset($user_info['position']) ? $user_info['position'] : null),
            'dob' => (isset($user_info['dob']) ? $user_info['dob'] : null),
            'fb_id' => (isset($user_info['fb_id']) ? $user_info['fb_id'] : null),
        );
        Registry::get('db')->beginTransaction();
        $user_query = "INSERT INTO user (fb_id, password, dob, position, email,  gender, first_name, last_name) "
                . "VALUES (:fb_id, :password, :dob, :position, :email, :gender, :first_name, :last_name);";
        $user_query = Registry::get('db')->prepare($user_query);
        $user_query->execute(
                array(
                    ":password" => Registry::get('system')->encrypt($user_info['password']),
                    ":position" => $user_info['position'],
                    ":email" => $user_info['email'],
                    ":gender" => $user_info['gender'],
                    ":first_name" => $user_info['firstname'],
                    ":last_name" => $user_info['lastname'],
                    ":dob" => $user_info['dob'],
                    ":fb_id" => $user_info['fb_id']
        ));
        $user_id = Registry::get('db')->lastInsertId();
        Registry::get('db')->commit();

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/User/Files/' . $user_id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        Registry::get('system')->create_zip($dir . "/root.zip", array(), TRUE);
        return $user_id;
    }

    function getId() {
        return $this->user_id;
    }
    function getAttr($attr, $id = NULL) {
        $set = false;
        if (!isset($id)) {
            $set = true;
            if(isset($this->attr[$attr]))
                return $this->attr[$attr];
            $id = $this->user_id;
        }
        $user_query = "SELECT :attr FROM user WHERE id = :user_id;";
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":attr" => $attr,":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->attr[$attr] = $user;
        }
        return $user;
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
            $user_query = "SELECT CONCAT(`first_name`, ' ' ,`last_name`)as name FROM user WHERE id = :user_id";
        }
        else if($name === 1) {
            $user_query = "SELECT first_name FROM user WHERE id = :user_id";
        }
        else if($name === 2) {
            $user_query = "SELECT last_name FROM user WHERE id = :user_id";
        }
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if($set) {
            $this->name[$name] = $user;
        }
        return $user;
    }

    function getConnections($id = NULL) {
        if(!isset($id)) {
            $id = $this->user_id;
        }
        $query = "SELECT IF(receiver_id = :user_id, user_id, receiver_id)as user_id FROM connection "
                . "WHERE user_id = :user_id OR receiver_id = :user_id;";
        $query = Registry::get('db')->prepare($query);
        $query->execute(array(
            ":user_id" => $id
        ));
        $connections = $query->fetchAll(PDO::FETCH_ASSOC);
        return Registry::get('system')->array_values_r($connections);
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
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
        $location_query = Registry::get('db')->prepare($location_query);
        $location_query->execute(array(":user_id" => $id));
        $location = $location_query->fetch(PDO::FETCH_ASSOC);
        if($set) {
            //$this->comun_name = $user;
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
        $sql = Registry::get('db')->prepare($sql);
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
    
//    function getComunityId($id = null) {
//        $set = false;
//        if (!isset($id)) {
//            $id = $this->user_id;
//            $set = true;
//            if(!is_null($this->comunity_id)) {
//                return $this->commnity_id;
//            }
//        }
//        $user_query = "SELECT comunity_id FROM user WHERE id = :user_id";
//        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//        $user_query->execute(array(":user_id" => $id));
//        $user = $user_query->fetchColumn();
//        if($set) {
//            $this->comunity_id = $user;
//        }
//        return $user;
//    }
//
//    function getComunityName($id = null) {
//        $set = false;
//        if (!isset($id)) {
//            $id = $this->user_id;
//            $set = true;
//            if(!is_null($this->comunity_name)) {
//                return $this->comunity_name;
//            }
//        }
//        $user_query = "SELECT name FROM comunity WHERE id = (SELECT commnity_id FROM user WHERE id = :user_id);";
//        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//        $user_query->execute(array(":user_id" => $id));
//        $user = $user_query->fetchColumn();
//        if($set) {
//            $this->commuity_name = $user;
//        }
//        return $user;
//    }

    function getProfilePicture($id = null) {
        $set = false;
        if ($id == null) {
            $id = $this->user_id;
        }
        $user_query = "SELECT path as full, thumb_path as thumb, icon_path as icon FROM file WHERE id = (SELECT profile_picture FROM user WHERE id = :user_id);";
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user_profile_picture = $user_query->fetch(PDO::FETCH_ASSOC);
        return $user_profile_picture;
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
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
        $user_query = Registry::get('db')->prepare($user_query);
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
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    function updateSettings($about = null, $year = null, $email = null, $language = null) {
        $sql = "UPDATE user SET ";
        if (isset($about)) {
            $sql .= 'about="' . $about . '"';
        }
        if (isset($email)) {
            $sql .= ',email="' . $email . '" ';
        }
        if (isset($language)) {
            $sql .= ',default_language="' . $language . '"';
        }
        $sql .= ' WHERE id=' . $this->user_id;
        $sql = Registry::get('db')->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sql->execute(array());
    }

    function getBookmarks($id = null) {
        if (!isset($id) || $id == "") {
            $id = $this->user_id;
        }
        $user_query = "SELECT id, name, link FROM bookmark WHERE user_id = :user_id";
        $user_query = Registry::get('db')->prepare($user_query);
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
        $who_liked_query = Registry::get('db')->prepare($who_liked_query);
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
        Registry::get('db')->prepare("UPDATE user SET last_activity = NOW() WHERE id = " . $id . ";")->execute();
    }

    function getOnline($id) {
        if ($id == null) {
            return true;
        }
        if($id === $this->user_id) {
            return true;
        }
        $id = (int)$id;
        if(is_int($id)) {
            $sql = "SELECT id FROM user WHERE id = " . $id . " AND last_activity > DATE_SUB(NOW(), INTERVAL 1 MINUTE);";
            $sql = Registry::get('db')->prepare($sql);
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
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":id" => $id,
            ":user_id" => $this->user_id
        ));
    }
    
    function get_user_preview($user_id = null) {
    	if(is_null($user_id)) {
    		$user_id = $this->user_id;
    	}
        $array = array();
        $array['id'] = $user_id;
        $array['name'] = $this->getName($user_id);
        $array['pic'] = $this->getProfilePicture($user_id);
        return $array;
    }

    function printTag($id) {
        $return = "<table cellspacing='0'>"
                . "<tr><td><a href='user?id=".$id."'><div class='profile_picture_medium' style='background-image: url(\"".$this->getProfilePicture("thumb", $id). "\");'></div></a>
                                        </td>
                                        <td>
                                            <p style='padding-left: 0px;' class='settings'>
                                                <a href='user?id=".$id."'>
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
        $this->setOnline();
        return $array;
    }

    function connect($user_id) {
        $sql = "INSERT INTO connection_invite(user_id, receiver_id) VALUES (:user_id, :receiver_id);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user_id,
            ":receiver_id" => $user_id
            ));
        //echo $user_id;
    }

    function connectAccept($invite_id) {
        $sql = "SELECT * FROM connection_invite WHERE id = :invite_id; UPDATE connection_invite SET status=1 WHERE id = :invite_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":invite_id" => $invite_id
            ));
        $info = $sql->fetch(PDO::FETCH_ASSOC);

        $sql = "INSERT INTO connection(user_id, receiver_id) VALUES (:user_id, :receiver_id);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => $info['user_id'],
            ":receiver_id" => $info['receiver_id']
            ));
    }

}
if ($_SERVER['REQUEST_METHOD'] == "POST") { require_once('declare.php');
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
            $array[] = $user->getPosition($_POST['id']);
            echo json_encode($array);
        } else if ($_POST['action'] == 'setOnline') {
            $user->setOnline();
        } else if($_POST['action'] == "getOnlineMass") {
            die(json_encode($user->getOnlineMass((array)$_POST['users'])));
        } else if ($_POST['action'] == "profile_picture") {
            $user->setProfilePicture($_POST['file_id']);
        } else if($_POST['action'] == "connect") {
            $user->connect($_POST['user_id']);
        } else if($_POST['action'] == "acceptInvite") {
            $user->connectAccept($_POST['invite_id']);
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === "connections") {
            require_once('declare.php');
            $cons = array(
                'Groups' => Registry::get('group')->getUserGroups(),
                'Connections' => Registry::get('user')->getConnections(),
                'Users' => array()
            );
            foreach($cons as $key => $value) {
                if($key === "Groups") {
                    foreach ($cons[$key] as $i => $value) {
                        $cons["Groups"][$i] = array();
                        $cons["Groups"][$i]['id'] = $value;
                        $cons["Groups"][$i]['name'] = Registry::get('group')->getName($value);
                        $cons["Groups"][$i]['pic'] = Registry::get('group')->getProfilePicture($value);
                        $members = Registry::get('group')->getMembers_Connections($value);
                         foreach ($members as $key => $value) {
                            $cons['Users'][$key] = array();
                            $cons['Users'][$key]['id'] = $value['id'];
                            $cons['Users'][$key]['name'] = Registry::get('user')->getName($value['id']);
                            $cons['Users'][$key]['pic'] = Registry::get('user')->getProfilePicture($value['id']);
                         }
                    }
                } else if($key === "Connections") {
                    foreach ($cons[$key] as $i => $value) {
                        $cons['Connections'][$i] = array();
                        $cons['Connections'][$i]['id'] = $value[0];
                        $cons['Connections'][$i]['name'] = Registry::get('user')->getName($value[0]);
                        $cons['Connections'][$i]['pic'] = Registry::get('user')->getProfilePicture($value[0]);
                    }
                }
            }
            die(json_encode($cons));
        }
    }
}
?>