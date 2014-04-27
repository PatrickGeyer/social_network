<?php
function print_body() {
    if (TRUE) { 
        global $feed_id;
        ?>
        <div class="container">
            <div class='contentblock home_feed_post_container'>
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
                                            <textarea tabindex='1' id="status_text" placeholder= "Update Status or Share Files..." class="status_text autoresize"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='post_content_wrapper'>
                                            <div class="post_media_wrapper">
                                                <div class='post_media_wrapper_background timestamp' style='text-align:left;'><span>Dropbox</span></div>
                                                <img class='post_media_loader' src='Images/ajax-loader.gif'></img>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style='width:30%;height:100%;position: relative;'>
                                <div id='file_share'>
                                    <table id='file_dialog' style='width:100%;' cellspacing="0" cellpadding="0">
                                        <?php
                                        foreach (Registry::get('files')->getList_r() as $file) {
                                            Registry::get('home')->fileList($file);
                                        }
                                        ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div id='post_more_options' class='post_more_options'>
                        <button class="post-button pure-button-green small">Post</button>
                        <button id='attach_file_button' class='pure-button-neutral smallest' style="cursor:pointer;" onclick="$('#post_file').trigger('click');">+</button>
                        <input type="file" name="file" id="post_file" multiple style='display:none;' />
                        <div class='default_dropdown_selector' style='display:inline-block;' wrapper_id='audience_selector'>
                            <span class='default_dropdown_preview'>Everyone</span>
                            <i class='fa fa-angle-down'></i>
                            <div class='default_dropdown_wrapper' style='display:none;' id='audience_selector'>
                                <ul class='default_dropdown_menu'>
                                    <li class='default_dropdown_item' controller_id='audience_selector' share_id='a'>
                                        <span>Everyone</span>
                                    </li>
                                    <?php
                                    foreach (Registry::get('group')->getUserGroups() as $single_group) {
                                        echo "<li class='default_dropdown_item' "
                                        . "controller_id='audience_selector' share_id='" . $single_group . "'>";
                                        echo "<span>" . Registry::get('group')->getName($single_group) . "</span>";
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
            <div id='feed_refresh'> 
                <div class='feed_container'>
                    <!--  Activity Here -->
                </div>
            </div>
        </div>
        <script>
            var share_group_id = <?php echo (is_int($feed_id) ? $feed_id : "'$feed_id'"); ?>;
            var activity_id = null;
            var Feed = new Application.prototype.Feed(share_group_id, 'home');
            Feed.min = <?php echo (isset($max) ? $max : '0'); ?>;
            Feed.get();
            Feed.onfetch = function() {
                $(function() {
                    $('.feed_container').prepend(Feed.print());
                });
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

                $('#status_text').focus(function() {
                    $(this).css('min-height', '100px');
                    $('#file_share').show();
                    $('#post_more_options').show();
                    $('.post_wrapper').css('padding-bottom', $('.post_more_options').height());
                    $('.post_media_wrapper').show();
                    $('#file_share').mCustomScrollbar("update");
                    $('.home_feed_post_container_arrow_border').css('border-right-color', 'rgb(70, 180,220)');
                }).focusout(function() {
                    $('.home_feed_post_container_arrow_border').css('border-right-color', 'lightgrey');
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
}
$min_activity_id = $user_id = $group_id = $filter = NULL;
if (isset($_GET['min_activity_id'])) {
    $min_activity_id = $_GET['min_activity_id'];
}
if (isset($_GET['fg'])) {
    $group_id = $feed_id = $_GET['fg'];
} else if (isset($_GET['f'])) {
    if ($_GET['f'] == 'a') {
        $filter = $feed_id = 'a';
    } else {
        $filter = $feed_id = $_GET['f'];
    }
} else if (isset($_GET['u'])) {
    $user_id = $_GET['u'];
    $feed_id = 'u_' . $user_id;
} else {
    $filter = $feed_id = 'a';
}
$page_identifier = "home";
require_once('Scripts/lock.php');
?>