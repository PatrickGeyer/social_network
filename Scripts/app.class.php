<?php
require_once 'declare.php';
class App {

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
        Registry::get('db')->beginTransaction();
        $sql = "INSERT INTO app.highscore (score, user_id, game_id) VALUES (:score, :user_id, :game_id);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":score" => $score,
            ":game_id" => $game_id,
            ":user_id" => Registry::get('user')->user_id,
        ));
        $game_id = Registry::get('db')->lastInsertId();
        Registry::get('db')->commit();
        return $game_id;
    }
    public function get($id) {
        $sql = "SELECT * FROM app.app WHERE id = :id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":id" => $id,
        ));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getHighscores($game_id, $min, $max) {
        Registry::get('db')->query("SET @curRank := 0;");
        $sql = "SELECT *, @curRank := @curRank + 1 AS rank FROM app.highscore WHERE game_id = :game_id "
                . "AND @curRank BETWEEN :min AND :max ORDER BY score DESC;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":game_id" => $game_id,
            ":min" => $min,
            ":max" => $max
        ));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        Registry::get('app')->create($_POST['name'], $_POST['type']);
    } else if ($_POST['action'] === 'setHighscore') {
        Registry::get('app')->highscore($_POST['score'], $_POST['game_id']);
    } 
} else if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['action'])) {
    switch($_GET['action']) {
        case "getHighscores" :
            die(json_encode(Registry::get('app')->getHighscores($_GET['game_id'], $_GET['min'], $_GET['max']), JSON_HEX_APOS));
    }
}