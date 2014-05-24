<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/Scripts/declare.php');
//         Base::$FB->destroySession();
	setcookie("id", "", -1, "/");
	header("location: ../login");
?>