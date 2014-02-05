<?php
include_once 'welcome.php';
include_once 'chat.php';
include_once 'friends_list.php';
$activity_id = $_GET['a'];
?>
<div class="container" id="home_container">
    <div class='home_feed_post_container' style="padding-top:0px;border: 0px;">
        <?php
        $home->homeify($home->getSingleActivity($activity_id));
        ?>
    </div>
</div>