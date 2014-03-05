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
            <div class="group_list">
                <ul style="width:100%;">
                    <?php
                    $groups = $group->getUserGroups();
                    foreach ($groups as $group_id) {
                        $valid0 = "";
                        echo "<li class='friend_list_group' >"
                        . "<a class='friend_list ellipsis_overflow' "
                        . "style='background-image:url(" . $group->getProfilePicture('chat', $group_id) . ");'"
                        . "href ='group?id=" . urlencode(base64_encode($group_id)) . "'>" . $group->getGroupName($group_id)
                        . "</a></li>";
                    }
                    if (isset($valid0)) {
                         echo "<script>$('.group_list').prepend('<p>Groups</p>');</script>";
                    }
                    ?>
                </ul>
            </div>
            <div class="connection_list">
                <ul style="width:100%;">
                    <?php
                    include_once('Scripts/lock.php')
                    ?>
                    <?php
                    $query = "SELECT receiver_id, user_id FROM connection WHERE user_id = :user_id OR receiver_id = :user_id;";
                    $query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $query->execute(array(":user_id" => $user->getId()));
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        $valid = true;
                        $user_id = $friends['user_id'];
                        if($friends['user_id'] == $user->user_id) {
                            $user_id = $friends['receiver_id'];
                        }
                        echo "<li class='" . ($user->getOnline($user_id) == true ? 'friend_list_on' : 'friend_list_off') . " profile_picture_".$user_id." user_preview' user_id='" . $user_id . "'>"
                        . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $user->getProfilePicture('chat', $user_id) . ");' 
                        href ='user?id=" . urlencode(base64_encode($user_id)) . "'>"
                        . $user->getName($user_id) . "</a></li>";
                    }
                    if (isset($valid)) {
                         echo "<script>$('.connection_list').prepend('<p>Connections</p>');</script>";
                    }
                    ?>
                </ul>
            </div>
            <div class='community_list'>
                <ul style="width:100%;">
                    <?php
                    $query = "SELECT name, id, position FROM user WHERE community_id = :user_school_id AND position = :user_position AND id != :user_id ORDER BY name;";
                    $query = $database_connection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $query->execute(array(":user_id" => $user->getId(), ":user_school_id" => $user->getCommunityId(), ":user_position" => $user->getPosition()));
                    if (!$query) {
                        die(print_r($database_connection->errorInfo()));
                    }
                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
                        $valid = true;
                        echo "<li class='" . ($user->getOnline($friends['id']) == true ? 'friend_list_on' : 'friend_list_off') . " profile_picture_".$friends['id']." user_preview' user_id='" . $friends['id'] . "'>"
                        . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $user->getProfilePicture('chat', $friends['id']) . ");' 
                        href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>"
                        . $friends['name'] . "</a></li>";
                    }
                    if (isset($valid)) {
                         echo "<script>$('.community_list').prepend('<p>Community</p>');</script>";
                    }
                    ?>
                </ul>
            </div>
            <div class="group_list_user">
                <ul style="width:100%;">
                    <?php
                    $groups = $group->getUserGroups();
                    foreach ($groups as $group_id) {
                        $members = $group->getMembers($group_id);
                        foreach ($members as $member) {
                                $valid6 = true;
                                echo "<li class='friend_list_on user_preview' user_id='" . $member . "'>"
                                . "<a class='friend_list ellipsis_overflow' style='background-image:url(" . $user->getProfilePicture('chat', $member) . ");' "
                                . "href ='user?id=" . urlencode(base64_encode($member)) . "'>" . $user->getName($member) . "</a></li>";
                            }
                    }
                    if (isset($valid6)) {
                         echo "<script>$('.group_list_user').prepend('<p>Group</p>');</script>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>


