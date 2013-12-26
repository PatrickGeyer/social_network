<?php

class Community {

    private $link;
    private $dsn;
    private $user = 'root';
    private $password;
    private $database_connection;

    public function __construct() {
        $this->username = 'root';
        $this->password = 'Filmaker1';
        $this->dsn = 'mysql:dbname=social_network;host=localhost';

        $this->link = new PDO($this->dsn, $this->user, $this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)) OR die("There was a problem connecting to the database.");

        return true;
    }

    function getId() {
        return base64_decode($_COOKIE['id']);
    }

    public function getName($id = null) {
        $user_query = "SELECT name FROM community WHERE id = :id";
        $user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getLeader($id) {
        $user_query = "SELECT leader FROM community WHERE id = :id";
        $user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getLeaderId($id) {
        $user_query = "SELECT leader_id FROM community WHERE id = :id";
        $user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getMembers($id) {
        $user_query = "SELECT * FROM users WHERE community_id = :id";
        $user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchAll();
        return $user;
    }

    public function getAbout($id) {
        $user_query = "SELECT about FROM community WHERE id = :user_id";
        $user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getProfilePicture($size = "original", $id) {
        $user_query = "SELECT profile_picture FROM community WHERE id = :user_id";
        $user_query = $this->link->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        if (!empty($user) || $user != "") {
            return $user;
        } else {
            return "Images/profile-picture-default-unknown-" . $size . ".jpg";
        }
    }
    
    public function getType($community_id)
    {
        $community_query = "SELECT type FROM community WHERE id = :community_id;";
        $community_query = $this->database_connection->prepare($community_query);
        
    }

}

?>