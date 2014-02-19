<?php

class Phrase {
    private $database_connection;
    private static $phrase = NULL;
    function __construct() {
        $this->database_connection = Database::getConnection();
    }
    
    public static function getInstance() {
        if (self :: $phrase) {
            return self :: $phrase;
        }

        self :: $phrase = new Phrase();
        return self :: $phrase;
    }
    
    function get($identify, $lan) {
        $sql = "SELECT * FROM language WHERE phrase_identifier = :id AND language = :lan;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":id" => $identify,
            ":lan" => $lan
        ));
        $return = $sql->fetch(PDO::FETCH_ASSOC);
        return $return['phrase'];
    }
}