<?php

if (!isset($_COOKIE['id']) || $_COOKIE['id'] == "") {
    header("location: ../login");
}
else {
    require_once('declare.php');
    $REQUEST = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : "FIRST");
    if ($REQUEST == "FIRST") {
        require_once 'js.php';
        require_once 'welcome.php';
        require_once 'chat.php';
        echo '<div class="global_container">';
        require_once 'left_bar.php';
        print_body();
        require_once 'right_bar.php';
        echo "</div>";
    }
}
?>