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

if (isset($_POST['search']) && $_POST['search'] == "universal") {
    echo "<script>$('#names_universal').show();</script>";
    foreach ($sql as $row) {
        $array = array();
        $array['name'] = $user->getName($row['id']);
        $array['div_onclick'] = "window.location.assign(&quot;user?id=" . urlencode(base64_encode($row['id'])) . "&quot;);";
        $array['img_src'] = $user->getProfilePicture('chat', $row['id']);
        $array['info'] = $system->trimStr($user->getCommunityName($row['id']), 25);
        $array['id'] = $row['id'];

        $suggestions++;
        if ($suggestions == 1) {
            $array['div_class'] = 'match';
        } else {
            $array['div_class'] = "name_selector";
        }
        searchify($array);
    }
    foreach ($group_sql as $row) {
        $suggestions++;
        $array = array();
        $array['name'] = $group->getGroupName($row['id']);
        $array['div_onclick'] = "window.location.assign(&quot;group?id=" . urlencode(base64_encode($row['id'])) . "&quot;);";
        $array['img_src'] = $group->getProfilePicture('chat', $row['id']);
        $array['info'] = $system->trimStr($user->getAbout($row['id']), 25);
        $array['id'] = $row['id'];

        $suggestions++;
        if ($suggestions == 1) {
            $array['div_class'] = 'match';
        } else {
            $array['div_class'] = "name_selector";
        }
        searchify($array);
    }
    //$return_data .= "<div style='padding:5; border-top:1px dotted grey; text-align:center;background-color:transparent; position:static; bottom:0;'>
    //<a class='search_option' href='search?q=".$searchTxt."'><small>Show all results</small></a></div>";
    if ($searchTxt == "") {
        $return_data = "<script>$('#names_universal').hide();</script>";
    }
} else if (isset($_POST['search']) && $_POST['search'] == "message") {
    echo "<script>$('.message_names').show();</script>";
    foreach ($sql as $row) {
        $suggestions++;
        if ($suggestions == 1) {
            $return_data = "<div class='match' onclick='addreceivermessage(" . $row['id'] . ", &quot;" . $user->getName($row['id']) . "&quot;);' id='match'>
			<img class='profile_picture' src='" . $user->getProfilePicture('icon', $row['id']) . "'></img>
			<span class='search_option_name'>" . $user->getName($row['id']) . "</span><br /><span class='search_option_info'>" . $user->getCommunityName($row['id']) . "&bull;Year " . $user->getPosition($row['id']) . "</span></div>";
        } else {
            $return_data .= "<div class='name_selector' onclick='addreceivermessage(" . $row['id'] . ", &quot;" . $user->getName($row['id']) . "&quot;);' id='" . $row['id'] . "'>
			<img class='profile_picture' src='" . $user->getProfilePicture('icon', $row['id']) . "'></img>
			<span class='search_option_name'>" . $user->getName($row['id']) . "</span><br /><span class='search_option_info'>" . $user->getCommunityName($row['id']) . "&bull;Year " . $user->getPosition($row['id']) . "</span></div>";
        }
    }
} else if (isset($_POST['search']) && $_POST['search'] == "share") {
    echo "<script>$('#names').show();</script>";

    foreach ($group_sql as $row) {
        $suggestions++;
        if ($suggestions == 1) {
            $return_data = "<div class='match' onclick='addreceivershare(&quot;group&quot;, " . $row['id'] . ", &quot;" . $group->getGroupName($row['id']) . "&quot;);' id='match'>
			<img class='profile_picture' src='" . $row['profile_picture_chat_icon'] . "'></img>
			<span class='search_option_name'>" . $group->getGroupName($row['id']) . "</span><br /><span class='search_option_info'>" . $group->getGroupAbout($row['id']) . "</span></div>";
        } else {
            $return_data .= "<div class='name_selector' onclick='addreceivershare(&quot;group&quot;, " . $row['id'] . ", &quot;" . $group->getGroupName($row['id']) . "&quot;);' id='" . $row['id'] . "'>
			<img class='profile_picture' src='" . $row['profile_picture_chat_icon'] . "'></img>
			<span class='search_option_name'>" . $group->getGroupName($row['id']) . "</span><br /><span class='search_option_info'>" . $group->getGroupAbout($row['id']) . "</span></div>";
        }
    }
    foreach ($sql as $row) {
        $suggestions++;
        if ($suggestions == 1) {
            $return_data = "<div class='match' onclick='addreceivershare(&quot;user&quot;, " . $row['id'] . ", &quot;" . $user->getName($row['id']) . "&quot;);' id='match'>
			<img class='profile_picture' src='" . $user->getProfilePicture('chat', $row['id']) . "'></img>
			<span class='search_option_name'>" . $user->getName($row['id']) . "</span><br /><span class='search_option_info'>" . $user->getCommunityName($row['id']) . "</span></div>";
        } else {
            $return_data .= "<div class='name_selector' onclick='addreceivershare(&quot;user&quot;, " . $row['id'] . ", &quot;" . $user->getName($row['id']) . "&quot;);' id='" . $row['id'] . "'>
			<img class='profile_picture' src='" . $user->getProfilePicture('chat', $row['id']) . "'></img>
			<span class='search_option_name'>" . $user->getName($row['id']) . "</span><br /><span class='search_option_info'>" . $user->getCommunityName($row['id']) . "</span></div>";
        }
    }
} else {
    echo "<script>$('#names').show();</script>";
    foreach ($sql as $row) {
        $array = array(
            "name" => $row['name']);
        $suggestions++;
        if ($searchTxt == $row['name']) {
            $return_data .= "<div class='match' onclick='addreceivergroup(" . $row['id'] . ", &quot;" . $row['name'] . "&quot;);' id='match1'>
			<img class='profile_picture' src='" . $row['profile_picture_chat_icon'] . "'></img>
			<span class='search_option_name'>" . $row['name'] . "</span><br /><span class='search_option_info'>" . $row['school'] . "</span></div>";
        }
        if ($suggestions == 1) {
            $return_data = "<div class='match' onclick='addreceivergroup(" . $row['id'] . ", &quot;" . $row['name'] . "&quot;);' id='match'>
			<img class='profile_picture' src='" . $row['profile_picture_chat_icon'] . "'></img>
			<span class='search_option_name'>" . $row['name'] . "</span><br /><span class='search_option_info'>" . $row['school'] . "</span></div>";
        } else {
            $return_data .= "<div class='name_selector' onclick='addreceivergroup(" . $row['id'] . ", &quot;" . $row['name'] . "&quot;);' id='" . $row['id'] . "'>
			<img class='profile_picture' src='" . $row['profile_picture_chat_icon'] . "'></img>
			<span class='search_option_name'>" . $row['name'] . "</span><br /><span class='search_option_info'>" . $row['school'] . "</span></div>";
        }
    }
}

if ($suggestions == 0) {
    $return_data .= "<div style='text-align:center;'><span class='school'>No Suggestions</span></div>";
}
if ($searchTxt == "") {
    $return_data .= "<script>$('#names').hide();</script>";
}

echo $return_data;

function searchify($record) {
    echo "<div class='" . $record['div_class'] . "' onclick='" . $record['div_onclick'] . "'>";
    echo "<img class='profile_picture' src='" . $record['img_src'] . "'></img>";
    echo "<span class='search_option_name'>" . $record['name'] . "</span><br /><span class='search_option_info'>" . $record['info'] . "</span></div>";
}

?>