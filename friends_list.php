<?php
$connected_users = array();
?>

<div class="contentblock group_list">
    <ul style="width:100%;">
        <?php
        $groups = Registry::get('group')->getUserGroups();
        foreach ($groups as $group_id) {
            $valid0 = "";
            echo "<li class='friend_list_group' >"
            . "<a class='friend_list ellipsis_overflow' "
            . "style='background-image:url(\"" . Registry::get('group')->getProfilePicture('chat', $group_id) . "\");'"
            . "href ='group?id=" . $group_id . "'>" . Registry::get('group')->getName($group_id)
            . "</a></li>";
        }
        ?>
        <li onclick="" class="friend_list_group">
            <a class="friend_list ellipsis_overflow"><i class='fa fa-plus'></i>&nbsp;<span>Create Group</span></a>
        </li>
        <?php
        //if (isset($valid0)) {
        echo "<script>$('.group_list').prepend('<b>Groups</b>');</script>";
        //}
        ?>
    </ul>
</div>
<div class="contentblock connection_list">
    <ul style="width:100%;">
        <?php
        foreach (Registry::get('user')->getConnections() as $connection) {
            $connection = $connection[0];
            if (!in_array($connection, $connected_users)) {
                $valid = true;
                echo "<li class='online_status user_preview' data-user_id='" . $connection . "'>"
                . "<a class='friend_list ellipsis_overflow' style='background-image:url(\"" . Registry::get('user')->getProfilePicture('chat', $connection) . "\");' 
                            href ='user?id=" . $connection . "'>"
                . Registry::get('user')->getName($connection) . "</a></li>";
                $connected_users[] = $connection;
            }
        }
        if (isset($valid)) {
            echo "<script>$('.connection_list').prepend('<b>Connections</b>');</script>";
        }
        ?>
    </ul>
</div>
<!--             <div class='contentblock community_list'> -->
<!--                 <ul style="width:100%;"> -->
<?php
//                    while ($friends = $query->fetch(PDO::FETCH_ASSOC)) {
//                        $valid = true;
//                        echo "<li class='online_status user_preview' data-user_id='" . $friends['id'] . "'>"
//                        . "<a class='friend_list ellipsis_overflow' style='background-image:url(\"" . Registry::get('user')->getProfilePicture('chat', $friends['id']) . "\");' 
//                        href ='user?id=" . urlencode(base64_encode($friends['id'])) . "'>"
//                        . $friends['name'] . "</a></li>";
//                    }
//                    if (isset($valid)) {
//                         echo "<script>$('.community_list').prepend('<div class=\"timestamp\"><span>Community</span></div>');</script>";
//                    }
?>
<!--                 </ul> -->
<!--             </div> -->
<div class="contentblock group_list_user">
    <ul style="width:100%;">
        <?php
        $groups = Registry::get('group')->getUserGroups();
        foreach ($groups as $group_id) {
            $members = Registry::get('group')->getMembers($group_id);
            foreach ($members as $member) {
                $member = $member[0];
                if (!in_array($member, $connected_users)) {
                    $valid6 = true;
                    echo "<li class='friend_list_on user_preview' user_id='" . $member . "'>"
                    . "<a class='friend_list ellipsis_overflow' style='background-image:url(\"" . Registry::get('user')->getProfilePicture('chat', $member) . "\");' "
                    . "href ='user?id=" . $member . "'>" . Registry::get('user')->getName($member) . "</a></li>";
                    $connected_users[] = $member;
                }
            }
        }
        if (isset($valid6)) {
            echo "<script>$('.group_list_user').prepend('<b>Group</b>');</script>";
        }
        ?>
    </ul>
</div>

