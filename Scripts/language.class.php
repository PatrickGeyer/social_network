<?php

class Language {

    public function getVars() {
        $lan = Registry::get('user')->getLanguage();

        $sql = "SELECT * FROM language.phrases WHERE language = :lan;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":lan" => $lan
        ));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}
