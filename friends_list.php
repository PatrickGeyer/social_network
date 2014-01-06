<head>
    <script>
        function show_group() {
            dialog(
                    content={
                        type: "html",
                        content: '<div id="dialog" title="Create a Group" style="overflow:hidden;margin-bottom:20px;"> <div class="pseudonym"> <table class="none" style="width:100%;"> <tr> <td> <input style="border-radius:0; width:100%;" id="group_name_input" type="text" autocomplete="off" onkeyup="if(this.value == &apos;&apos;) {$(&apos;#group_warning&apos;).show(); } else {$(&apos;#group_warning&apos;).hide(); }"placeholder="Group Name" /> </td> <td style="display:none;" id="group_warning"><div class="warning_red">Group name cannot be blank!</div> </td> </tr> <tr> <td><textarea style="border-radius:0; width:100%; height:100px;" id = "group_about" class="thin" style="width:100%; height: 100px;" placeholder="About..." autocomplete="off" type="text"></textarea></td> </tr> <tr> <td> <div style="border-radius:0;"> </div> </td> </tr> </table> <table class="none" style="width:100%;"> <tr> <td><input autocomplete="off" style="border-radius:0; width:100%;" onkeyup="getnamesgroup(this.value);" type="text" id="names_input" placeholder="Add Member..." /></td> </tr> <tr> <td id ="names_slot"></td> </tr> </table> <div class="scroll_medium" style="display:none;overflow:auto;max-height:100px; position:relative; padding:2px;border: 1px solid lightgrey; background-color:white;" id="names"> </div> </div> </div>'
                    },
            buttons={
                type: "success",
                text: "Create",
                onclick: function(){createGroup();dialogLoad();}
            },
            properties={
                modal: false,
                title: "Create a Group"
            });
        }
    </script>
    <script>
        var auto_refresh = setInterval(
                function()
                {
                    $('#friends_load').load('friends_list.php #friends_load');
                }, 5000);
    </script>

    <script>
        function getnamesgroup(value)
        {
            $.post("Scripts/searchbar.php", {search: "group", input_text: value}, function(data) {
                $('#names').empty();
                $('#names').append(data);
            });
        }
    </script>
    <script>
        var invited_members = [];
        function addreceivergroup(new_receiver, new_receiver_name)
        {
            var found = $.inArray(new_receiver, invited_members);
            if (found != -1)
            {

            }
            else
            {
                invited_members.push(new_receiver);
                $('#names_input').val("");
                var html = "<tr><td style='min-width:100px;' id = '" + new_receiver + "'> \
                    <div class='tag-triangle'></div><div class='added_name'><span style='font-family:century gothic;'>" + new_receiver_name + "</span> \
                    <span class='delete_receiver' onclick='removereceiver(" + new_receiver + ");'>x \
                    </span></div></td></tr>";
                $('#names_slot').before(html);
            }
        }
        function removereceiver(receiver_id)
        {
            var index = invited_members.indexOf(receiver_id);
            if (index > -1)
            {
                invited_members.splice(index, 1);
            }
            $('#' + receiver_id).remove();
        }
        function createGroup()
        {
            var group_name = $('#group_name_input').val();
            if (group_name != "")
            {
                var group_about = $('#group_about').val();
                var group_type = $("#dialog input[type='radio']:checked").val();
                $.post("creategroup.php", {group_name: group_name, group_about: group_about, group_type: group_type, invited_members: invited_members}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("group?id=" + status[1]);
                    }
                });
            }
            else
            {

            }
        }
    </script>

</head>
<div id='friends_container'>
    <div id="friends_bar" class="scroll_thin" style='overflow-x:hidden;'>
        <div id="friend_load">
            <div id="friend_on">
                <ul style="width:100%;">
                    <?php
                    include_once('Scripts/lock.php')
                    ?>
                    <?php
                    $query = "SELECT * FROM users WHERE community_id = :user_school_id AND position = :user_position AND id != :user_id ORDER BY name;";
                    $query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $query->execute(array(":user_id" => $user->getId(), ":user_school_id" => $user->getCommunityId(), ":user_position" => $user->getPosition()));
                    if (!$query) {
                        die(print_r($database_connection->errorInfo()));
                    }
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        $valid = "";
                        echo "<li class='" . ($user->getOnline($friends['id']) == true ? 'friend_list_on' : 'friend_list_off') . " user_preview' user_id='".$friends['id']."'>"
                                . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $user->getProfilePicture('icon', $friends['id']) . ");' 
						href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>"
                        . $friends['name'] . "</a></li>";
                    }
                    if (isset($valid)) {
                        // echo "<script>$('#friend_on').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- online classmates -</small>');</script>";
                    }
                    ?>
                </ul>
            </div>
            <div id="group_list">
                <ul style="width:100%;">
<?php
$groups = $group->getUserGroups();
foreach ($groups as $group_id) {
    $valid2 = "";
    $query_group = "SELECT * FROM `group` WHERE id = :group_id;";
    $query_group = $database_connection->prepare($query_group, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $query_group->execute(array(":group_id" => $group_id));
    if (!$query_group) {
        die($database_connection->errorInfo());
    }
    if (!$group_info = $query_group->fetch(PDO::FETCH_ASSOC)) {
        //die (print_r($database_connection->errorInfo()));
    }
    echo "<li class='friend_list_group group_preview' >
						<a class='friend_list ellipsis_overflow' style='background-image:url(" . $group->getProfilePicture('icon', $group_info['id']) . ");'
						href ='group?id=" . urlencode(base64_encode($group_info['id'])) . "'>" . $group_info['group_name'] . "</a></li>";
    $friend_query = "SELECT * FROM group_member WHERE group_id = :group_id AND member_id != :user_id";
    $friend_query = $database_connection->prepare($friend_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $friend_query->execute(array(":user_id" => $user->getId(), ":group_id" => $group_id));
    while ($friends_in_group = $friend_query->fetch(PDO::FETCH_ASSOC)) {
        $friend_group_query = "SELECT * FROM users WHERE id = :member_id;";
        $friend_group_query = $database_connection->prepare($friend_group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $friend_group_query->execute(array(":member_id" => $friends_in_group['member_id']));
        foreach ($friend_group_query->fetchAll() as $friend_profile) {
            if ($user->getOnline($friend_profile['id']) == true) {
                echo "<li class='friend_list_on user_preview' user_id='".$friends_in_group['member_id']."'>"
                        . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friend_profile['profile_picture_chat_icon'] . ");' 
									href ='user?id=" . urlencode(base64_encode($friend_profile['id'])) . "'>" . $friend_profile['name'] . "</a></li>";
            } else {
                echo "<li class='friend_list_off user_preview' user_id='".$friends_in_group['member_id']."'>"
                        . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friend_profile['profile_picture_chat_icon'] . ");' 
									href ='user?id=" . urlencode(base64_encode($friend_profile['id'])) . "'>" . $friend_profile['name'] . "</a></li>";
            }
        }
    }
}
while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
    if ($user->getOnline($friends['id']) == true) {
        echo "<li class='friend_list_on user_preview' user_id='".$friends['id']."'>"
                . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friends['profile_picture_chat_icon'] . ");' 
							href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
    } else {
        echo "<li class='friend_list_off user_preview' user_id='".$friends['id']."'>"
                . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friends['profile_picture_chat_icon'] . ");' 
							href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
    }
}
if (isset($valid2)) {
    // echo "<script>$('#group_list').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- group -</small>');</script>";
}
?>
                </ul>
            </div>
            <div id="school_list">
                <ul style="width:100%;">
                    <?php
                    $query = "SELECT * FROM users WHERE community_id = :user_school_id AND position != :user_position;";
                    $query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $query->execute(array(":user_school_id" => $user->getCommunityId(), ":user_position" => $user->getPosition()));
                    if (!$query) {
                        die($database_connection->errorInfo());
                    }
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        $valid3 = "";
                        if ($user->getOnline($friends['id']) == true) {
                            echo "<li class='friend_list_on user_preview' user_id='".$friends['id']."'>"
                                    . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friends['profile_picture_chat_icon'] . ");' 
							href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
                        } else {
                            echo "<li class='friend_list_off user_preview' user_id='".$friends['id']."'>"
                                    . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friends['profile_picture_chat_icon'] . ");' 
							href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
                        }
                    }
                    if (isset($valid3)) {
                        // echo "<script>$('#school_list').prepend('<small style=\'margin-left:-20px;line-height:20px;\'>- school -</small>');</script>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <button style='margin-top:10px;' class='pure-button-primary smallest' onclick='show_group();' title='Create a Group'>Create Group</button>
</div>
</div>


