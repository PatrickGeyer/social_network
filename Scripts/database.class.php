<?php
include_once("base.class.php");

class Database extends Base {
    private static $link = null ;

    public static function getConnection ( ) {
        if (self :: $link) {
            return self :: $link;
        }

        $dsn = "mysql:dbname=social_network;host=localhost";
        $db_user = "social_network";
        $db_password = "Filmaker1";

        self :: $link = new PDO($dsn, $db_user, $db_password);
        return self :: $link;
    }

}

?>