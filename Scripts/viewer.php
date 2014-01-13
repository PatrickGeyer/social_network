<?php
include 'lock.php';

$file_id = $_GET['id'];

$sql = "SELECT * FROM files WHERE id = :file_id;";
$sql = $database_connection->prepare($sql);
$sql->execute(array(
    ":file_id" => $file_id
));
$file = $sql->fetch(PDO::FETCH_ASSOC);
if(empty($file)) {
    echo "No such File Found";
}
else {
    $file['filepath'] = $_SERVER['DOCUMENT_ROOT']."/".$file['filepath'];
//    $fp = fopen ($file['filepath'], "r");
//    ob_start();
    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="'.$file['name'].'"');
    header("Content-length: ".filesize($file['filepath']));
//    ob_end_flush();
//    while(!feof($fp)) {
//        $file_buffer = fread($fp, 2048);
//        echo $file_buffer;
//    }
// 
//    fclose($fp);
//    die();
    @readfile($file['filepath']);
}


?>