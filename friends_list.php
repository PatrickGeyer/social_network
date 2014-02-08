<head>
    <script>
        $(function() {
            $('.friends_bar').mCustomScrollbar(SCROLL_OPTIONS);
            $(document).on('input', '#names_input', function() {
                search($(this).val(), 'group', '.group_search_results', function() {
                });
            });
            $(window).on('resize', function() {
                $('.friends_bar').mCustomScrollbar("update");
            });
        });

        var auto_refresh = setInterval(
                function()
                {
                    $('#friends_load').load('friends_list.php #friends_load');
                }, 10000);

    </script>
</head>
<div id='friends_container'>
    <div id="friends_bar" class="friends_bar" style='overflow-x:hidden;width:100%;height:100%;'>
        <div id="friend_load">
            <div id="friend_on">
                <ul style="width:100%;">
                    <?php
                    include_once('Scripts/lock.php')
                    ?>
                    <?php
                    $query = "SELECT name, id, position FROM users WHERE community_id = :user_school_id AND position = :user_position AND id != :user_id ORDER BY name;";
                    $query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $query->execute(array(":user_id" => $user->getId(), ":user_school_id" => $user->getCommunityId(), ":user_position" => $user->getPosition()));
                    if (!$query) {
                        die(print_r($database_connection->errorInfo()));
                    }
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        $valid = "";
                        echo "<li class='" . ($user->getOnline($friends['id']) == true ? 'friend_list_on' : 'friend_list_off') . " user_preview' user_id='" . $friends['id'] . "'>"
                        . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $user->getProfilePicture('chat', $friends['id']) . ");' 
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
                        $query_group = "SELECT id, group_name, group_about FROM `group` WHERE id = :group_id;";
                        $query_group = $database_connection->prepare($query_group, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $query_group->execute(array(":group_id" => $group_id));
                        if (!$query_group) {
                            die($database_connection->errorInfo());
                        }
                        if (!$group_info = $query_group->fetch(PDO::FETCH_ASSOC)) {
                            //die (print_r($database_connection->errorInfo()));
                        }
                        echo "<li class='friend_list_group group_preview' >"
                        . "<a class='friend_list ellipsis_overflow' "
                        . "style='background-image:url(" . $group->getProfilePicture('chat', $group_info['id']) . ");'"
                        . "href ='group?id=" . urlencode(base64_encode($group_info['id'])) . "'>" . $group_info['group_name']
                        . "</a></li>";
                        $friend_query = "SELECT user_id FROM group_member WHERE group_id = :group_id AND member_id != :user_id";
                        $friend_query = $database_connection->prepare($friend_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $friend_query->execute(array(":user_id" => $user->getId(), ":group_id" => $group_id));
                        while ($friends_in_group = $friend_query->fetch(PDO::FETCH_ASSOC)) {
                            $friend_group_query = "SELECT id, name, position FROM users WHERE id = :member_id;";
                            $friend_group_query = $database_connection->prepare($friend_group_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                            $friend_group_query->execute(array(":member_id" => $friends_in_group['member_id']));
                            foreach ($friend_group_query->fetchAll() as $friend_profile) {
                                if ($user->getOnline($friend_profile['id']) == true) {
                                    echo "<li class='friend_list_on user_preview' user_id='" . $friends_in_group['member_id'] . "'>"
                                    . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friend_profile['profile_picture_chat_icon'] . ");' 
                                    href ='user?id=" . urlencode(base64_encode($friend_profile['id'])) . "'>" . $friend_profile['name'] . "</a></li>";
                                }
                                else {
                                    echo "<li class='friend_list_off user_preview' user_id='" . $friends_in_group['member_id'] . "'>"
                                    . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friend_profile['profile_picture_chat_icon'] . ");' 
                                    href ='user?id=" . urlencode(base64_encode($friend_profile['id'])) . "'>" . $friend_profile['name'] . "</a></li>";
                                }
                            }
                        }
                    }
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        if ($user->getOnline($friends['id']) == true) {
                            echo "<li class='friend_list_on user_preview' user_id='" . $friends['id'] . "'>"
                            . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $friends['profile_picture_chat_icon'] . ");' 
                            href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
                        }
                        else {
                            echo "<li class='friend_list_off user_preview' user_id='" . $friends['id'] . "'>"
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
                    $query = "SELECT id, name, position FROM users WHERE community_id = :user_school_id AND position != :user_position;";
                    $query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $query->execute(array(":user_school_id" => $user->getCommunityId(), ":user_position" => $user->getPosition()));
                    if (!$query) {
                        die($database_connection->errorInfo());
                    }
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        $valid3 = "";
                        if ($user->getOnline($friends['id']) == true) {
                            echo "<li class='friend_list_on user_preview' user_id='" . $friends['id'] . "'>"
                            . "<a class='friend_list ellipsis_overflow' "
                            . "style='background-image:url(" . $user->getProfilePicture('chat', $friends['id']) . ");' "
                            . "href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
                        }
                        else {
                            echo "<li class='friend_list_off user_preview' user_id='" . $friends['id'] . "'>"
                            . "<a class='friend_list ellipsis_overflow' "
                            . "style='background-image:url(" . $user->getProfilePicture('chat', $friends['id']) . ");' "
                            . "href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>" . $friends['name'] . "</a></li>";
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
</div>


