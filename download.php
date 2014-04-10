<?php
require "Scripts/declare.php";
$file_id = $_GET['id'];
header("Content-Type: application/octet-stream");
header("Content-disposition: attachment; filename=" . urlencode(pathinfo(Registry::get('files')->getAttr($file_id, 'path'), PATHINFO_BASENAME)));
readfile(Registry::get('files')->getAttr($file_id, 'path'));