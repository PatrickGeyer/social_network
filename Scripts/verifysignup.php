<?php

include_once('declare.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = (isset($_POST['password']) ? $_POST['password'] : null);
    $firstname = (isset($_POST['firstname']) ? $_POST['firstname'] : null);
    $lastname = (isset($_POST['lastname']) ? $_POST['lastname'] : null);
    $name = $firstname . " " . $lastname;
    $community = (isset($_POST['community_id']) ? $_POST['community_id'] : null);
    $gender = (isset($_POST['gender']) ? $_POST['gender'] : null);
    $email = (isset($_POST['email']) ? $_POST['email'] : null);
    $position = (isset($_POST['position']) ? $_POST['position'] : null);

    $user_query = "SELECT id, name, community_id, position FROM user WHERE email = :email";
    $user_query = $database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $user_query->execute(array(":email" => $email));
    $count = $user_query->rowCount();
    $user = $user_query->fetch(PDO::FETCH_ASSOC);

    if ($count == 1) {
        echo '<p style = "background-color:orange;">This email has already been registered by ' . $user['name'] . ' from ' . $user['school'] . '?</p>';
        //setcookie("id", $user['id'], time()+3600000, '/');
        //setcookie("chat_feed", 1, time()+3600000, '/');  
    }
    else {

//        $school_query = "SELECT id, name FROM community WHERE name = :school LIMIT 1";
//        $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//        $school_query->execute(array(":school" => $community));
//        $community = $school_query->fetch(PDO::FETCH_ASSOC);
//
//        if ($community['name'] == "") { //if there isn't already a school then make row, add one member and appoint user as leader...
//            $school_query = "INSERT INTO community (name, leader) VALUES (:school, :school_name);";
//            $school_query = $database_connection->prepare($school_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//            $school_query->execute(array(":school" => $community, ":school_name" => $newname, ":user_id" => $user['id']));
//            $dir = 'School/Files/' . $schoolarray['id'];
//            if (!file_exists($dir)) {
//                mkdir($dir, 0777);
//            }
//        }
//        else { //if there's already a school then..
//        }
        $database_connection->beginTransaction();
        $user_query = "INSERT INTO user (name, password, community_id, position, email,  gender, first_name, last_name) "
                . "VALUES (:name, :password, :community_id, :position, :email, :gender, :first_name, :last_name);";
        $user_query = $database_connection->prepare($user_query);
        $user_query->execute(
                array(
                    ":name" => $name,
                    ":password" => $system->encrypt($password),
                    ":community_id" => $community,
                    ":position" => $position,
                    ":email" => $email,
                    ":gender" => $gender,
                    ":first_name" => $firstname,
                    ":last_name" => $lastname
        ));
        $user_id = $database_connection->lastInsertId();
        $database_connection->commit();

//        $create_files_entry = "INSERT INTO files(user_id, path, folder_id) VALUES (:user_id, :path, :folder_id);";
//        $create_files_entry = $database_connection->prepare($create_files_entry, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//        $create_files_entry->execute(array(":user_id" => $user['id'], ":path" => "Users/Files/" . $user['id'], ":folder_id" => 1));

        $dir = '../User/Files/' . $user_id;
        if(!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $system->create_zip($dir."/root.zip", array(), TRUE);
        
        setcookie("id", base64_encode($user_id), time() + 3600000, '/');
        setcookie("chat_feed", 's', time() + 3600000, '/');
        setcookie("home_feed", 'a', time() + 3600000, '/');
    }
}
?>