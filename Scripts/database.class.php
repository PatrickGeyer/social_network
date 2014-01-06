<?php
include_once("base.class.php");

class Database extends Base {
    private static $link = null ;

    public static function getConnection ( ) {
        if (self :: $link) {
            return self :: $link;
        }

        $dsn = "mysql:dbname=social_network;host=localhost";
        $user = "social_network";
        $password = "Filmaker1";

        self :: $link = new PDO($dsn, $user, $password);
        return self :: $link;
    }

}

?>