<?php
include_once('Scripts/lock.php');

$min_activity_id = $user_id = $group_id = $filter = NULL;
if (isset($_GET['min_activity_id'])) {
    $min_activity_id = $_GET['min_activity_id'];
}

if (isset($_GET['fg'])) {
    $group_id = $feed_id = $_GET['fg'];
}
else if (isset($_GET['f'])) {
    if($_GET['f'] == 'a') {
        $filter = $feed_id = 'a';
    }
    else {
        $filter = $feed_id = $_GET['f'];
    }
    
}
else if (isset($_GET['u'])) {
    $user_id = $_GET['u'];
    $feed_id = 'u_'.$user_id;
} 
else {
    $filter = $feed_id = 'a';
}

$activity_query = $entity->getActivityQuery($filter, $group_id, $user_id, $min_activity_id);

// if (isset($_GET['min_activity_id'])) {
//     die("<script>min_activity_id = " . $home->getActivity($activity_query, $min_activity_id) . ";</script>");
// }
$page_identifier = "home";

include_once('welcome.php');
include_once('chat.php');
?>

<html>
    <head>	
        <script>
            $('.icon').click(function() {
                $('.icon').fadeTo('slow', 0.5);
            });
            function openDialog() {
                $("#status_image_selector").click();
            }

            var share_group_id = <?php echo (is_int($feed_id) ? $feed_id : "'$feed_id'"); ?>;
            var activity_id = null;
            $(function($)
            {
                //getFeedContent(share_group_id, min_activity_id, 'home', function(){});
                getFeed(share_group_id, 'home', min_activity_id, activity_id, function(response){
                    var string = '';
                    for (var i in response) {
                        string += homify(response[i]);
                    }
                    $('.feed_container').prepend(string);
                });

                $(document).on('click', '.home_like_icon', function() {
                    var has_liked = $(this).attr('has_liked');
                    if(has_liked == "false") {
                        $(this).text(COMMENT_UNLIKE_TEXT);
                        $(this).attr('has_liked', 'true')
                    }
                    else {
                        $(this).text(COMMENT_LIKE_TEXT);
                        $(this).attr('has_liked', 'false')
                    }
                });

                $("#post_file").change(function(e)
                {
                    uploadFile($(this), 'addToStatus');
                });

                $('#status_text').focus(function() {
                    $(this).css('min-height', '100px');
                    $('#file_share').parent('td').css('width', '200px');
                    $('#file_share').show();
                    $('#post_more_options').show();
                    $('.post_wrapper').css('padding-bottom', $('.post_more_options').height());
                    $('.post_media_wrapper').show();
                    $('#file_share').mCustomScrollbar("update");
                    $('.home_feed_post_container_arrow_border').css('border-right-color', 'rgb(70, 180,220)');
                }).focusout(function() {
                    $('.home_feed_post_container_arrow_border').css('border-right-color', 'lightgrey');
                });

                $('#status_text').on('input', function() {
                    $(this).css('height', '0px');
                    $(this).css('height', $(this)[0].scrollHeight + "px");
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
        </script>
        <title>Home</title>
    </head>

    <body>
        <div class='global_container'>
            <?php include_once 'left_bar.php';?>
            <div class="container">
                <div class='home_feed_post_container'>
                    <div class='home_feed_post_container_arrow_border'>
                        <div class='home_feed_post_container_arrow'></div>
                    </div>
                    <div class='post_wrapper'>
                        <table style='width:100%;' cellspacing='0' cellpadding='0'>
                            <tr>
                                <td>
                                    <table style='width:100%;' cellspacing='0' cellpadding='0'>
                                        <tr style='height:100%;'>
                                            <td>
                                                <textarea tabindex='1' id="status_text" placeholder= "Update Status or Share Files..." class="status_text scroll_thin"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class='post_content_wrapper'>
                                                <div class="post_media_wrapper">
                                                    <div class='post_media_wrapper_background post_comment_time'>Dropbox</div>
                                                    <img class='post_media_loader' src='Images/ajax-loader.gif'></img>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style='width:00px;height:100%;position: relative;'>
                                    <div id='file_share'>
                                        <table id='file_dialog' style='width:100%;' cellspacing="0" cellpadding="0">
                                            <?php
                                            foreach ($files->getList_r() as $file) {
                                                $home->fileList($file);
                                            }
                                            ?>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div id='post_more_options' class='post_more_options'>
                            <button onclick="submitPost();" class="pure-button-success small">Post</button>
                            <button id='attach_file_button' class='pure-button-neutral smallest' style="cursor:pointer;" onclick="$('#post_file').trigger('click');">+</button>
                            <input type="file" name="file" id="post_file" multiple style='display:none;' />
                            <div class='default_dropdown_selector' style='display:inline-block;' wrapper_id='audience_selector'>
                                <span class='default_dropdown_preview'>Everyone</span>
                                <div class='default_dropdown_wrapper' style='display:none;' id='audience_selector'>
                                    <ul class='default_dropdown_menu'>
                                        <li class='default_dropdown_item' controller_id='audience_selector' share_id='a'>
                                            <span>Everyone</span>
                                        </li>
                                        <li class='default_dropdown_item' controller_id='audience_selector' share_id='s'>
                                            <span><?php echo $user->getCommunityName(); ?></span>
                                        </li>
                                        <li class='default_dropdown_item' controller_id='audience_selector' share_id='y'>
                                            <span>Year <?php echo $user->getPosition(); ?></span>
                                        </li>
                                        <?php
                                        foreach ($group->getUserGroups() as $single_group) {
                                            echo "<li class='default_dropdown_item' "
                                            . "controller_id='audience_selector' share_id='" . $single_group . "'>";
                                            echo "<span>" . $group->getGroupName($single_group) . "</span>";
                                            echo "</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>	
                    <div style="width:100%" id="progress_bar_holder"></div>
                </div>
                <div class='feed_wrapper_scroller scroll_thin_horizontal' style='margin-top:0px;margin-bottom: 20px;'>
                        <table>
                            <tr>
                                <td>
                                    <div id='a' feed_id='home' filter_id = 'a' class="feed_selector home_feed_selector 
                                    <?php
                                    if ($feed_id === 'a') {
                                        echo 'active_feed';
                                    }
                                    ?>">All</div>
                                </td>
                                <td>
                                    <div id='s' feed_id='home' filter_id = 's' class="feed_selector home_feed_selector 
                                    <?php
                                    if ($feed_id === 's') {
                                        echo 'active_feed';
                                    }
                                    ?>">
                                             <?php
                                             echo $system->trimStr($user->getCommunityName(), 15);
                                             ?>
                                    </div>
                                </td>
                                <td>
                                    <div id='y' feed_id='home' filter_id = 'y' class="feed_selector home_feed_selector 
                                    <?php
                                    if ($feed_id === 'y') {
                                        echo 'active_feed';
                                    }
                                    ?>">Year <?php
                                             echo $user->getPosition();
                                             ?>
                                    </div>
                                </td>
                                <?php
                                foreach ($group->getUserGroups() as $single_group) {
                                    echo '<td><div feed_id="home" style="border-bottom:3px solid blue;" id="' . $single_group . '" filter_id = "' . $single_group . '" class="feed_selector home_feed_selector ' . ($feed_id == $single_group ? "active_feed" : "") . '">' . $system->trimStr($group->getGroupName($single_group), 15) . '</div></td>';
                                }
                                ?>
                            </tr>
                        </table>
                    </div>
                <div id='feed_refresh'> 
                    <div class='feed_container'>
                        <!--  Activity Here -->
                    </div>
                </div>
            </div>
            <?php include_once 'right_bar.php';?>
        </div>
        <script>
            min_activity_id = <?php echo (isset($max) ? $max : '0'); ?>;
            function showhide(element)
            {
                $(element).toggle("slide");
            }

        </script>
    </body>
</html>