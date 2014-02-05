<?php

class Community {
    private static $community = NULL;
    private $database_connection;

    public function __construct() {
        $this->database_connection = Database::getConnection();
        return true;
    }
    public static function getInstance ( ) {
        if (self :: $community) {
            return self :: $community;
        }

        self :: $community = new Community();
        return self :: $community;
    }
    function getId() {
        return base64_decode($_COOKIE['id']);
    }

    public function getName($id = null) {
        $user_query = "SELECT name FROM community WHERE id = :id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getLeader($id) {
        $user_query = "SELECT leader FROM community WHERE id = :id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getLeaderId($id) {
        $user_query = "SELECT leader_id FROM community WHERE id = :id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getMembers($id) {
        $user_query = "SELECT id, year, name, joined FROM users WHERE community_id = :id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":id" => $id));
        $user = $user_query->fetchAll();
        return $user;
    }

    public function getAbout($id) {
        $user_query = "SELECT about FROM community WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":user_id" => $id));
        $user = $user_query->fetchColumn();
        return $user;
    }

    public function getProfilePicture($size = "original", $id) {
        $user_query = "SELECT profile_picture FROM community WHERE id = :user_id";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
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
    
    function getCommunities() {
        $user_query = "SELECT name, id, profile_picture FROM community;";
        $user_query = $this->database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array());
        $user = $user_query->fetchAll();
        return $user;
    }

}

?>