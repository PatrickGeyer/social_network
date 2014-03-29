<?php

require_once('user.class.php');
require_once('system.class.php');

class Entity extends System{

    private static $entity;
    private $user;

    public function __construct() {
        parent::__construct();
        $this->user = User::getInstance($args = array());
    }

    static function getInstance($args = array()) {
        if (self :: $entity) {
            return self :: $entity;
        }

        self :: $entity = new Entity();
        return self :: $entity;
    }

    /**
      /* getActivityQuery -->
      /* Finds and prepares query to get activity
      /* Params
      /* 1. $filter (school, year, all)
      /* 2. $group_id
      /* 3. $user_id
      /* 4. $min_activity_id (default = 0)
     */
    function getActivityQuery($filter = NULL, $group_id = NULL, $user_id = NULL, $min_activity_id = 0, $activity_id = NULL) {
        $this->user = User::getInstance($args = array());
        $min_activity_id_query = "AND id >" . ($min_activity_id == 0 ? "=" . $min_activity_id : $min_activity_id);
        if(isset($activity_id)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id = :activity_id AND visible = 1 ORDER BY time DESC";
            $activity_query = $this->database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $activity_query->execute(array(
                ":activity_id" => $activity_id
            ));
        } else if (isset($group_id)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id IN "
                    . "(SELECT activity_id FROM activity_share WHERE group_id = :group_id AND direct = 1) "
                    . "AND visible = 1 " . $min_activity_id_query . " ORDER BY time DESC";
            $activity_query = $this->database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $activity_query->execute(array(":group_id" => urldecode($group_id)));
        } else if (isset($user_id)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id IN "
                    . "(SELECT activity_id FROM activity_share WHERE "
                    . "group_id in (SELECT group_id FROM group_member WHERE user_id = :user_id) "
                    . "OR user_id = :user_id))"
                    . " AND visible = 1 AND user_id = :user_id " . $min_activity_id_query . " ORDER BY time DESC";
            $activity_query = $this->database_connection->prepare($activity_query);
            $activity_query->execute(array(":user_id" => $user_id));
        }
        if (!isset($activity_query)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id IN "
                    . "(SELECT activity_id FROM activity_share WHERE "
                    . "group_id in (SELECT group_id FROM group_member WHERE user_id = :user_id) "
                    . "OR user_id = :user_id)"
                    . " AND visible = 1 " . $min_activity_id_query . " ORDER BY time DESC";
            $activity_query = $this->database_connection->prepare($activity_query);
            $activity_query->execute(array(
                ":user_id" => $this->user->user_id,
            ));
        }
//        die($filter);
        return $activity_query;
    }

}

?>