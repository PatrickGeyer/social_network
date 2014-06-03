<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Scripts/declare.php';

class App {

	public function __construct() {
		if(!class_exists('Registry')) {
            include_once $_SERVER['DOCUMENT_ROOT'].'/Scripts/declare.php';
        }
	}

    public function create($name, $type) {
        Registry::get('db')->startTransaction();
        $sql = "INSERT INTO app.app (user_id, name, type) VALUES (:user_id, :name, :type);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":name" => $name,
            ":user_id" => Registry::get('user')->user_id,
            ":type" => $type
        ));
        $game_id = Registry::get('db')->lastInsertId;
        Registry::get('db')->commit();
        return $game_id;
    }

    public function highscore($score, $game_id) {
        $sql = "INSERT INTO app.highscore (score, user_id, game_id) VALUES (:score, :user_id, :game_id);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":score" => $score,
            ":game_id" => $game_id,
            ":user_id" => Registry::get('user')->user_id,
        ));
//         return $game_id;
    }
    
    public function getPopularApps() {
        $sql = "CALL app.getPopularApps();";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute();
        
        $r = $sql->fetchAll(PDO::FETCH_ASSOC);
        $sql->closeCursor();
        foreach($r as $key => $val) {
            $r[$key] = $this->get($val['id']);
        }
        return $r;
    }
    
    public function getPic($id) {
        return array('thumb' => '/images/icons/app/thumb.png');
    }
    public function getMode($id) {
        return 0;
    }

    public function get($id) {
        $id = (int) $id;
        $sql = "SELECT * FROM `app`.`app` WHERE `app`.`id` = :id";
        $sql = Registry::get('db')->prepare($sql);
        Registry::get('db')->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql->execute(array(
            ":id" => (int) $id,
        ));

        $r = array();
        $r['info'] = $sql->fetch(PDO::FETCH_ASSOC);
        $r['pic'] = $this->getPic($id);
        return $r;
    }
    public function getAll($id) {
        $array = array();
        $array['info'] = $this->get($id);
        $array['pic'] = $this->getPic($id);
        $array['mode'] = $this->getMode($id);
        return $array;
    }

    public function getHighscores($game_id, $min = 0, $max = 9) {
        $sql = "CALL app.getHighscores(:game_id, :min, :max);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":game_id" => $game_id,
            ":min" => $min,
            ":max" => $max,
        ));
        
        $high = $sql->fetchAll(PDO::FETCH_ASSOC);
        $sql->closeCursor();
//        foreach ($high as $key => $value) {
//            $high[$key] = $this->formatHighscore($value);
//        }
        return $high;
    }

    public function getHighscore($game_id) {
        Registry::get('db')->query("SET @curRank := 0;");
        $sql = "SELECT *, @curRank := @curRank + 1 AS rank FROM app.highscore WHERE game_id = :game_id "
                . "AND user_id = :user_id ORDER BY score DESC;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":game_id" => $game_id,
            ":user_id" => Registry::get('user')->user_id,
        ));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function formatHighscore($item) {
        $item['name'] = Registry::get('user')->getName($item['user_id'], 3);
        return $item;
    }

}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        Registry::get('app')->create($_POST['name'], $_POST['type']);
    }
    else if ($_POST['action'] === 'setHighscore') {
        Registry::get('app')->highscore($_POST['score'], $_POST['game_id']);
        die(json_encode(array()));
    }
}
else if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case "getHighscores" :
            die(json_encode(Registry::get('app')->getHighscores($_GET['game_id'], $_GET['min'], $_GET['max']), JSON_HEX_APOS));
        case "getHighscore" :
            die(json_encode(Registry::get('app')->getHighscore($_GET['game_id']), JSON_HEX_APOS));
        case "getPopularApps" :
            die(json_encode(Registry::get('app')->getPopularApps(), JSON_HEX_APOS));
    }
}