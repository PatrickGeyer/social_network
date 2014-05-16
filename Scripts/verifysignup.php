<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('declare.php');
    $user_info = $_POST;

    $user_query = "SELECT id, name, position FROM user WHERE email = :email";
    $user_query = Registry::get('db')->prepare($user_query);
    $user_query->execute(array(":email" => $user_info['email']));
    $count = $user_query->rowCount();
    $user_info1 = $user_query->fetch(PDO::FETCH_ASSOC);

    if ($count == 1) {
        echo '<p style = "background-color:orange;">This email has already been Registryed by ' 
        . $user_info1['firstname'] . ' from ' . $user_info1['school'] . '?</p>';
    }
    else {
        $user_id = Registry::get('user')->create($user_info);
        setcookie("id", $user_id, time() + 3600000, '/');
        //setcookie("chat_feed", 's', time() + 3600000, '/');
    }
}
?>