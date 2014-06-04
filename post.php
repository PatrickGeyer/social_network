<?php
function print_body() { 
    $activity_id = $_GET['id'];
    ?>
<div class="container" id="home_container">
    <div class='post_container'>
        <script>
            var Feed = new Application.prototype.Feed(<?php echo $activity_id; ?>, 'activity', {container: $('.post_container')});
            Feed.get();
        </script>
    </div>
</div>
<?php }
 require_once 'Scripts/lock.php';