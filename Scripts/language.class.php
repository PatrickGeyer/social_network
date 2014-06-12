<?php

class Language {

    public function getVars() {
        $lan = Registry::get('user')->getLanguage();

        $sql = "SELECT phrase_id, text FROM language.phrase WHERE language = :lan;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":lan" => $lan
        ));
        $lan = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($lan as $key => $value) {
            $lan[$value['phrase_id']] = $value['text'];
            unset($lan[$key]);
        }
        return $lan;
    }

}
