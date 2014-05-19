<?php
require "Scripts/declare.php";
$file_id = $_GET['id'];
$path = Registry::get('files')->getAttr($file_id, 'path');
header("Content-Length: " . filesize($path));
header("Content-Type: application/octet-stream");
header("Content-disposition: attachment; filename=" . urlencode(Registry::get('files')->getAttr($file_id, 'name')));
readfile($path);