<?php

$return_data = "";
$suggestions = 0;
include_once('lock.php');
$searchTxt = $_POST['input_text'];
$return_data;

$sql = "SELECT id FROM users WHERE INSTR(`name`, '{$searchTxt}') > 0;";
$sql = $database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sql->execute();
$sql = $sql->fetchAll(PDO::FETCH_ASSOC);


$group_sql = "SELECT id FROM `group` WHERE INSTR(`group_name`, '{$searchTxt}') > 0;";
$group_sql = $database_connection->prepare($group_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$group_sql->execute();
$group_sql = $group_sql->fetchAll(PDO::FETCH_ASSOC);

function searchDiv($user_id, $community_id, $group_id, $div_class, $div_onclick, $img_src, $name, $info) {
    $return = "<div class='".$div_class."' onclick='".$div_onclick."'>"
            . "<img class='profile_picture' src='".$img_src."'></img>"
            . "<span class='search_option_name'>".$name."</span><br /><span class='search_option_info'>" . $info . "</span></div>";
    return $return;
}

if (isset($_POST['search']) && $_POST['search'] == "universal") {
    
    foreach ($sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                $row['id'], 
                NULL, 
                NULL, 
                $class, 
                "window.location.replace(\"user?id=" . base64_encode($row['id'])."\');", 
                $user->getProfilePicture('chat', $row['id']), 
                $user->getName($row['id']), 
                $user->getAbout($row['id']));
    }
    foreach ($group_sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                $row['id'], 
                NULL, 
                NULL, 
                $class, 
                "window.location.replace(\"user?id=" . base64_encode($row['id'])."\');", 
                $group->getProfilePicture('chat', $row['id']), 
                $group->getGroupName($row['id']), 
                $group->getAbout($row['id']));
    }
} else if (isset($_POST['search']) && $_POST['search'] == "message") {
    echo "<script>$('.message_names').show();</script>";
    foreach ($sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                $row['id'], 
                NULL, 
                NULL, 
                $class, 
                "addreceivermessage(" . $row['id'] . ", &quot;" . $user->getName($row['id']) . "&quot;);", 
                $user->getProfilePicture('chat', $row['id']), 
                $user->getName($row['id']), 
                $user->getAbout($row['id']));
    }
} else if (isset($_POST['search']) && $_POST['search'] == "share") {
    foreach ($group_sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                $row['id'], 
                NULL, 
                NULL, 
                $class, 
                "", 
                $group->getProfilePicture('chat', $row['id']), 
                $group->getGroupName($row['id']), 
                $group->getAbout($row['id']));
    }
    foreach ($sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                $row['id'], 
                NULL, 
                NULL, 
                $class, 
                "", 
                $user->getProfilePicture('chat', $row['id']), 
                $user->getName($row['id']), 
                $user->getAbout($row['id']));
    }
} else {
    foreach ($sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                $row['id'], 
                NULL, 
                NULL, 
                $class, 
                "addReceiver(&quot;group&quot;, " . $row['id'] . ", &quot;" . $group->getGroupName($row['id']) . "&quot;);", 
                $user->getProfilePicture('chat', $row['id']), 
                $user->getName($row['id']), 
                $user->getAbout($row['id']));
    }
}

if ($suggestions == 0) {
    $return_data .= "<div style='text-align:center;'><span class='school'>No Suggestions</span></div>";
}

echo $return_data;

?>