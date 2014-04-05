<?php

$return_data = "";
$suggestions = 0;
include_once('lock.php');
$searchTxt = $_POST['input_text'];
$return_data;

$sql = "SELECT id FROM user WHERE INSTR(CONCAT(`first_name`, ' ', `last_name`), '{$searchTxt}') > 0;";
$sql = Registry::get('db')->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sql->execute();
$sql = $sql->fetchAll(PDO::FETCH_ASSOC);


$group_sql = "SELECT id FROM `group` WHERE INSTR(`group_name`, '{$searchTxt}') > 0;";
$group_sql = Registry::get('db')->prepare($group_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$group_sql->execute();
$group_sql = $group_sql->fetchAll(PDO::FETCH_ASSOC);


$files_sql = "SELECT id FROM `file` WHERE INSTR(`name`, '{$searchTxt}') > 0;";
$files_sql = Registry::get('db')->prepare($files_sql);
$files_sql->execute();
$files_sql = $files_sql->fetchAll(PDO::FETCH_ASSOC);

function searchDiv($type, $entity_id, $div_class, $div_onclick, $img_src, $name, $info) {
    $entity = array("name" => $name, 'type' => $type, 'id' => $entity_id);
    $return = "<div data-entity='".json_encode($entity, JSON_HEX_APOS)."' class='search_option ".$div_class."' onclick=''>"
            . "<table style='width:100%;'><tr><td rowspan='2' style='width:35px;'>"
            . $img_src
            . "</td><td>"
            . "<p class='search_option_name ellipsis_overflow'>".$name."</p></td>";
            if($type == "user") {
                $return .= "<td><div class='connect_button'></div></td>";
            }
            $return .= "</tr>"
            . "<tr><td><span class='search_option_info'>" . $info . "</span></td></tr></table></div>";
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
                "user",
                $row['id'], 
                $class, 
                "window.location.replace(\"user?id=" . base64_encode($row['id'])."\");", 
                "<img class='profile_picture_medium' src='".$user->getProfilePicture('chat', $row['id'])."'></img>", 
                $user->getName($row['id']), 
                $user->getAbout($row['id']));
    }
    foreach ($files_sql as $row) {
        $file = Registry::get('files')->getInfo($row['id']);
        $file = array(
            "id" => $row['id'],
            "name" => Registry::get('files')->getName($row['id']),
            "parent_folder_id" => Registry::get('files')->getParentFolder($row['id']),
            "description" => Registry::get('files')->getDescription($row['id']),
            "type" => Registry::get('files')->getType('', $row['id']),
            "thumb_path" => Registry::get('files')->getPath($row['id'], "icon"),
            );
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                "file",
                $row['id'], 
                $class, 
                "window.location.replace(\"files?f=" . $row['id'] . "\");", 
                Registry::get('files')->tinyPreview($file),
                $file['name'],
                $file['description']
                );
    }
    foreach ($group_sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                "group",
                $row['id'], 
                $class, 
                "window.location.replace(\"user?id=" . base64_encode($row['id'])."\');", 
                "<img class='profile_picture_medium' src='".Registry::get('group')->getProfilePicture('chat', $row['id'])."'></img>", 
                Registry::get('group')->getGroupName($row['id']), 
                Registry::get('group')->getAbout($row['id']));
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
                "user",
                $row['id'], 
                $class, 
                "addreceivermessage(" . $row['id'] . ", &quot;" . $user->getName($row['id']) . "&quot;);", 
                "<img class='profile_picture_medium' src='".$user->getProfilePicture('chat', $row['id'])."'></img>",
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
                "group",
                $row['id'], 
                NULL, 
                $class, 
                "", 
                "<img class='profile_picture_medium' src='".Registry::get('group')->getProfilePicture('chat', $row['id'])."'></img>", 
                Registry::get('group')->getGroupName($row['id']), 
                Registry::get('group')->getAbout($row['id']));
    }
    foreach ($sql as $row) {
        $suggestions++;
        $class = 'name_selector';
        if($suggestions == 1) {
            $class = 'match';
        } 
        echo searchDiv(
                "user",
                $row['id'], 
                $class, 
                "", 
                "<img class='profile_picture_medium' src='".$user->getProfilePicture('chat', $row['id'])."'></img>", 
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
                "user",
                $row['id'], 
                $class, 
                "addReceiver(invited_members, " . $row['id'] . ", false, false, function(){addGroupReceiver(&quot;".$user->getName($row['id'])."&quot, ".$row['id'].")});", 
                "<img class='profile_picture_medium' src='".$user->getProfilePicture('chat', $row['id'])."'></img>", 
                $user->getName($row['id']), 
                $user->getAbout($row['id']));
    }
}

if ($suggestions == 0) {
    $return_data .= "<div style='text-align:center;'><span class='school'>No Suggestions</span></div>";
}

echo $return_data;

?>