<?php
	include_once('declare.php');
//         Base::$FB->destroySession();
	setcookie("id", "", -1, "/");
	header("location: ../login");
?>