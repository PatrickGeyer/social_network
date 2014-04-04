<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('declare.php');
    $user_info = array(
        'password' => (isset($_POST['password']) ? $_POST['password'] : 'social'),
        'firstname' => (isset($_POST['firstname']) ? $_POST['firstname'] : null),
        'lastname' => (isset($_POST['lastname']) ? $_POST['lastname'] : null),
        'name' => $firstname . " " . $lastname,
        'gender' => (isset($_POST['gender']) ? $_POST['gender'] : null),
        'email' => (isset($_POST['email']) ? $_POST['email'] : null),
        'position' => (isset($_POST['position']) ? $_POST['position'] : null),
        );
   

    $user_query = "SELECT id, name, position FROM user WHERE email = :email";
    $user_query = $database_connection->prepare($user_query);
    $user_query->execute(array(":email" => $email));
    $count = $user_query->rowCount();
    $user_info = $user_query->fetch(PDO::FETCH_ASSOC);

    if ($count == 1) {
        echo '<p style = "background-color:orange;">This email has already been Registryed by ' 
        . $user_info['name'] . ' from ' . $user_info['school'] . '?</p>';
    }
    else {
        $user_id = $user->create($user_info);
        setcookie("id", base64_encode($user_id), time() + 3600000, '/');
        //setcookie("chat_feed", 's', time() + 3600000, '/');
    }
}
?>