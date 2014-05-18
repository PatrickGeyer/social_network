<?php

function print_body() {
    $group_id = $_GET['id'];
    $is_member = Registry::get('group')->isMember(Registry::get('user')->user_id, $group_id);


    if (isset($_GET['f']) && $_GET['f'] == 'f') {
        $feed_id = 'f';
    }
    else {
        $feed_id = 'p';
    }
    ?>
    <head>
        <title>Group - <?php echo Registry::get('group')->getName($group_id); ?></title>
        <script>
            function leaveGroup(group_id) {
                $.post("Scripts/group.class.php", {action: "leave", group_id: group_id}, function(response) {
                    var status = response.split("/");
                    if (status[0] == "success") {
                        window.location.replace("group?id=" + status[1]);
                    }
                });
            }
            function deleteGroup(group_id) {
                $.post("Scripts/group.class.php", {action: "deleteG", group_id: group_id}, function(response) {
                    var status = response.split("/");
                    if (status[0] == "success") {
                        window.location.replace("home");
                    }
                });
            }
            function abdicateGroup(group_id) {
                $.post("Scripts/group.class.php.php", {action: "abdicate", group_id: group_id}, function(response) {
                    var status = response.split("/");
                    if (status[0] == "success") {
                        window.location.replace("home");
                    }
                });
            }
        </script>
    </head>
    <div class="container">
        <div class='group_feed'>
            <script>
                var group = new Application.prototype.User(<?php echo json_encode(Registry::get('group')->get_preview($group_id)); ?>);
                $(".group_feed").append(group.printHeader());
    <?php if ($feed_id == "p") { ?> $(".user_feed").append(group.printFeed()); <?php }
    else {
        ?> $(".group_feed").append(group.printSharedFiles()); <?php } ?>
            </script>
        </div>
    </div>
    <?php
}

require 'Scripts/lock.php';
?>