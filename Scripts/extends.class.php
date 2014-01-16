<?php

include_once('database.class.php');

class Extend {

    private $user_id;
    private $database_connection = NULL;
    
    public function __construct() {
        $this->user_id = base64_decode($_COOKIE['id']);
        $this->database_connection = Database::getConnection();
        return true;
    }

    function getImageSize($src) {
        if ($src[0] != ".") {
            $src = "../" . $src;
        }
        list($width, $height, $type, $attr) = getimagesize($src);
        $array[0] = $width;
        $array[1] = $height;
        echo json_encode($array);
    }

    function submitComment($comment, $post_id) {
        if ($comment != '') {
            $sql = "INSERT INTO comments (commenter_id, post_id, comment_text) VALUES (" . $this->user_id . ", :post_id, :comment);";
            $sql = $this->database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sql->execute(array(":post_id" => $post_id, ":comment" => $comment));
        }
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $extend = new Extend;

    if (isset($_POST['action_image'])) {
        $extend->getImageSize($_POST['src']);
    }
    if (isset($_POST['action']) && $_POST['action'] == 'submitComment') {
        $extend->submitComment($_POST['comment_text'], $_POST['post_id']);
    }
}