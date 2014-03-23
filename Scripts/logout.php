<?php
	include_once('lock.php');
        Base::$FB->destroySession();
	setcookie("id", "", -1, "/");
	header("location: ../login");
?>