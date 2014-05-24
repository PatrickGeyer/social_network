<?php

function print_body() {
    global $feed_id;
    ?>
    <div class="container">
        <div class='contentblock home_feed_post_container createPost'></div>
        <div id='feed_refresh'> 
            <div class='feed_container'>
                <!--  Activity Here -->
            </div>
        </div>
    </div>
    <script>
        var share_group_id = <?php echo (is_int($feed_id) ? $feed_id : "'$feed_id'"); ?>;
        var activity_id = null;
        var Feed = new Application.prototype.Feed(share_group_id, 'home', {container: $('.feed_container')});
        Feed.min = <?php echo (isset($max) ? $max : '0'); ?>;
        Feed.get();
        Feed.onfetch = function() {
            Feed.print();
        }
        $(function($) {
            $(document).on('click', '.home_like_icon', function() {
                var has_liked = $(this).attr('has_liked');
                if (has_liked == "false") {
                    $(this).text(COMMENT_UNLIKE_TEXT);
                    $(this).attr('has_liked', 'true')
                }
                else {
                    $(this).text(COMMENT_LIKE_TEXT);
                    $(this).attr('has_liked', 'false')
                }
            });



            $('.default_dropdown_item').click(function()
            {
                $('div[wrapper_id="' + $(this).attr('controller_id') + '"]').find('.default_dropdown_preview').text($(this).text());
                share_group_id = $(this).attr('share_id');
                //console.log(share_group_id);
            });
            $('#file_share').mCustomScrollbar(SCROLL_OPTIONS);
            $('#file_share').mCustomScrollbar("update");

        });

        function clearPostArea()
        {
            $('#status_text').val('').blur();
            $('#status_text').css('min-height', '35px');
            $('#status_text').css('height', 'auto');
            $('#file_share').hide();
            $('#post_more_options').hide();
            $('.post_media_wrapper').hide().find('.post_media_single').remove();
            $('.post_wrapper').css('padding-bottom', '0px');
            $('.post_media_wrapper_background').show();
            post_media_added_files = new Array();
            added_URLs = new Array();
            typed_URLs = new Array();
        }
        document.title = 'Home';
    </script>
    <?php
}

$min_activity_id = $user_id = $group_id = $filter = NULL;
if (isset($_GET['min_activity_id'])) {
    $min_activity_id = $_GET['min_activity_id'];
}
if (isset($_GET['fg'])) {
    $group_id = $feed_id = $_GET['fg'];
}
else if (isset($_GET['f'])) {
    if ($_GET['f'] == 'a') {
        $filter = $feed_id = 'a';
    }
    else {
        $filter = $feed_id = $_GET['f'];
    }
}
else if (isset($_GET['u'])) {
    $user_id = $_GET['u'];
    $feed_id = 'u_' . $user_id;
}
else {
    $filter = $feed_id = 'a';
}
$page_identifier = "home";
require_once($_SERVER['DOCUMENT_ROOT'].'/Scripts/lock.php');
?>