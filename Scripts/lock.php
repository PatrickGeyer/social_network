<?php

if (!isset($_COOKIE['id']) || $_COOKIE['id'] == "") {
    header("location: ../login");
}
else {
    require_once($_SERVER['DOCUMENT_ROOT'].'/Scripts/declare.php');
    $REQUEST = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : "FIRST");
    if ($REQUEST == "FIRST") {
        require_once $_SERVER['DOCUMENT_ROOT'].'/Scripts/js.php';
        if(isset($_GET['developer'])) { ?>
            <script>
                Application.prototype.UI.prop.leftBar = false;
                Application.prototype.UI.prop.rightBar = false;
            </script>
        <?php
            $developer = true;
            require_once $_SERVER['DOCUMENT_ROOT'].'/welcome.php';
        } else {
            require_once $_SERVER['DOCUMENT_ROOT'].'/welcome.php';
        }
        
        echo '<div class="global_container"><div class="content">';
        require_once $_SERVER['DOCUMENT_ROOT'].'/left_bar.php';
        require_once $_SERVER['DOCUMENT_ROOT'].'/right_bar.php';

        print_body();
        echo "</div></div>";
    }
    else {
        echo '<div class="global_container"><div class="content">';
        require_once $_SERVER['DOCUMENT_ROOT'].'/left_bar.php';
        if(!isset($_GET['developer'])) {
            require_once $_SERVER['DOCUMENT_ROOT'].'/right_bar.php';
        }

        print_body();
        echo "</div></div>";
    }
}
?>