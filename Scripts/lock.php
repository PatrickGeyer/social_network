<?php

if (!isset($_COOKIE['id']) || $_COOKIE['id'] == "") {
    header("location: ../login");
} else {
    include_once('declare.php');
}
?>