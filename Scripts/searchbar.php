<?php

$return_data = array();
$suggestions = 0;
include_once('declare.php');
$searchTxt = $_POST['input_text'];

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

function searchDiv($type, $entity_id) {
    $entity = array('entity_type' => $type, 'id' => $entity_id);
    if($type == "user") {
        $entity['first'] = Registry::get('user')->getName($entity_id, 1);
        $entity['last'] = Registry::get('user')->getName($entity_id, 2);
        $entity['name'] = Registry::get('user')->getName($entity_id, 3);
        $entity['info'] = Registry::get('user')->getAbout($entity_id) || 'No Info';
        $entity['img'] = Registry::get('user')->getProfilePicture('icon', $entity_id);
    } else if($type == 'group') {
        $entity['name'] = Registry::get('group')->getName($entity_id);
        $entity['info'] = Registry::get('group')->getAbout($entity_id);
        $entity['img'] = Registry::get('group')->getProfilePicture('icon', $entity_id);
    } else if($type == "file") {
        $entity['name'] = Registry::get('files')->getName($entity_id);
        $entity['parent_folder_id'] = Registry::get('files')->getParentFolder($entity_id);
        $entity["description"] = Registry::get('files')->getDescription($entity_id);
        $entity["type"] = Registry::get('files')->getType(Registry::get('files')->mime_content_type(Registry::get('files')->getAttr($entity_id, 'path'), $entity_id));
        $entity["img"] = Registry::get('files')->getFileTypeImage($entity, "THUMB");
    }
    return $entity;
}

if (isset($_POST['search']) && $_POST['search'] == "universal") {
    
    foreach ($sql as $row) {
        $return_data[] = searchDiv("user", $row['id']);
    }
    foreach ($files_sql as $row) {
        $return_data[] = searchDiv("file", $row['id']);
    }
    foreach ($group_sql as $row) {
        $return_data[] = searchDiv("group", $row['id']);
    }
} else if (isset($_POST['search']) && $_POST['search'] == "message") {
    foreach ($sql as $row) {
        $return_data[] = searchDiv("user", $row['id']);
    }
} else if (isset($_POST['search']) && $_POST['search'] == "share") {
    foreach ($group_sql as $row) {
        $return_data[] = searchDiv("group", $row['id']);
    }
    foreach ($sql as $row) {
        $return_data[] = searchDiv("user", $row['id']);
    }
} else {
    foreach ($sql as $row) {
        $return_data[] = searchDiv("user", $row['id']);
    }
}

echo json_encode($return_data, JSON_HEX_APOS);

?>