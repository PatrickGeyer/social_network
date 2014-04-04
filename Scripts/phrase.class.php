<?php

class Phrase {
    private static $phrase = NULL;
    function __construct() {
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
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":id" => $identify,
            ":lan" => $lan
        ));
        $return = $sql->fetch(PDO::FETCH_ASSOC);
        return $return['phrase'];
    }
}