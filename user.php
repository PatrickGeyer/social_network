<?php

function print_body() {

    if (isset($_GET['id'])) {
        $userid = $_GET['id'];
        if ($userid == Registry::get('user')->getId()) {
            $page_identifier = 'user';
        }
    }
    else {
        $userid = Registry::get('user')->user_id;
    }


    if (isset($_GET['t']) && $_GET['t'] == 'f') {
        $feed_id = 'f';
    }
    else {
        $feed_id = 'p';
    }
    ?>
    <script>document.title = "<?php echo $name = Registry::get('user')->getName($userid); ?>";</script>
    <div class='container'>
        <div class='header'></div>
        <div class="user_feed">
            <script>
                var user = new Application.prototype.User(<?php echo json_encode(Registry::get('user')->get_user_preview($userid)); ?>);
                user.printHeader({container: $('.header'), tab: "<?php echo $feed_id;?>"});
    <?php if ($feed_id == "p") { ?> user.printFeed({container: $(".user_feed")}); <?php }
    else { ?>user.printSharedFiles({container: $('.user_feed')}); <?php } ?>
            </script>

            <?php
//                if ($userid != Registry::get('user')->getId()) {
//                    echo "<button style='float:right;' class='pure-button-blue connect_button'>Connect</button><br />";
//                    echo "<div style='background-image:none;padding-right:0px;float:right;' wrapper_id='invite_selector' class='default_dropdown_actions'>
//				<button class='pure-button-blue connect_button'>Invite</button>";
//                    echo "<div id='invite_selector' class='default_dropdown_wrapper' style='display:none;float:right;'>";
//                    echo "<ul class='default_dropdown_menu'>";
//                    foreach (Registry::get('group')->getUserGroups() as $users_group) {
//                        $query1 = "SELECT group_id FROM group_member WHERE user_id = :user_id AND group_id = :group_id;";
//                        $query1 = Registry::get('db')->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//                        $query1->execute(array(":user_id" => $userid, "group_id" => $users_group));
//                        $query1 = $query1->fetchColumn();
//
//                        if ($query1 == "") {
//                            $name = Registry::get('group')->getName($users_group);
//                            echo "<script name='text_append'>$('#invite_text_holder').show();</script>";
//                            echo "<li class='default_dropdown_item' "
//                            . "onclick='showInvite(\"" . $name
//                            . "\", " . $userid . ", " . $users_group . ");'>"
//                            . $name . "</li>";
//                        }
//                    }
//                    echo "</ul>";
//                    echo "</div>";
//                }
            ?>


        </div>
    </div>
    <?php
}

require_once('Scripts/lock.php');
