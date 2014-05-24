<?php

class Entity {

    private static $entity;

    public function __construct() {
        if (!class_exists('Registry')) {
            include_once $_SERVER['DOCUMENT_ROOT'].'/Scripts/declare.php';
        }
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
    function getActivityQuery($filter = NULL, $group_id = NULL, $user_id = NULL, $min = 0, $max = 482734279, $activity_id = NULL) {
        Registry::setup();
        $order_by = "ORDER BY (
	( 
		(
			((SELECT COUNT(*) FROM activity_vote WHERE activity_id = activity.id AND vote_value != 0) + 
			(SELECT COUNT(*) FROM comment WHERE activity_id = activity.id AND visible=1)) * 2 +
			1
		) 
	) / 
	POW
	( 
		TIMESTAMPDIFF(SECOND, activity.time, NOW()), 1.5)
) DESC";
        $min_activity_id_query = "AND id BETWEEN " . $min . " AND " . $max;
        if (isset($activity_id)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id = :activity_id AND visible = 1 ORDER BY time DESC";
            $activity_query = Registry::get('db')->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $activity_query->execute(array(
                ":activity_id" => $activity_id
            ));
        }
        else if (isset($group_id)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id IN "
                    . "(SELECT activity_id FROM activity_share WHERE group_id = :group_id AND direct = 1) "
                    . "AND visible = 1 " . $min_activity_id_query . " ORDER BY time DESC";
            $activity_query = Registry::get('db')->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $activity_query->execute(array(":group_id" => urldecode($group_id)));
        }
        else if (isset($user_id)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id IN "
                    . "(SELECT activity_id FROM activity_share WHERE "
                    . "group_id in (SELECT group_id FROM group_member WHERE user_id = :user_id) "
                    . "OR user_id = :user_id)"
                    . " AND visible = 1 AND user_id = :user_id " . $min_activity_id_query . " ORDER BY time DESC";
            $activity_query = Registry::get('db')->prepare($activity_query);
            $activity_query->execute(array(":user_id" => $user_id));
        }
        if (!isset($activity_query)) {
            $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE id IN "
                    . "(SELECT activity_id FROM activity_share WHERE"
                    . constant('Base::CAN_VIEW_POST')
                    . ") AND visible = 1 " . $min_activity_id_query . " " . $order_by . " LIMIT 20";
            $activity_query = Registry::get('db')->prepare($activity_query);
            $activity_query->execute(array(
                ":user_id" => Registry::get('user')->user_id,
            ));
        }
        return $activity_query;
    }

}

?>