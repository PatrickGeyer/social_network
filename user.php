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
        </div>
    </div>
    <?php
}

require_once('Scripts/lock.php');
