<?php
include_once('Scripts/lock.php');
$page_identifier = "home";

include_once('welcome.php');
include_once('chat.php');

if (isset($_GET['fg'])) {
    $feed_id = $_GET['fg'];

    $activity_query = "SELECT * FROM activity WHERE 
	id IN (SELECT activity_id FROM activity_share WHERE group_id = :group_id AND direct = 1)
	AND visible = 1
	ORDER BY time DESC";
    $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $activity_query->execute(array(":group_id" => urldecode($_GET['fg'])));
}
else if (isset($_GET['f'])) {
    if ($_GET['f'] == 's') {
        $feed_id = 's';

        $activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share 
			WHERE community_id = :community_id AND direct=1) ORDER BY time DESC";
        $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":community_id" => $user->getCommunityId()));
    }
    else {
        $feed_id = 'y';

        $activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
			community_id = :community_id AND year = :user_year AND direct = 1) AND visible = 1 ORDER BY time DESC";
        $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":community_id" => $user->getCommunityId(), ":user_year" => $user->getPosition()));
    }
}
else {
    $feed_id = 'a';

    $activity_query = "SELECT * FROM activity WHERE id IN 
	(SELECT activity_id FROM activity_share WHERE 
		(community_id = :community_id 
			OR (year = :user_year AND community_id = :community_id) 
			OR group_id in 
			(SELECT group_id FROM group_member WHERE member_id = :user_id) 
			OR receiver_id = :user_id))
	AND visible = 1 ORDER BY time DESC";
    $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $activity_query->execute(array(":user_id" => $user->getId(), ":community_id" => $user->getCommunityId(), ":user_year" => $user->getPosition()));
}
?>
<!doctype html>
<html>
    <head>	
        <script>
            $('.icon').click(function() {
                $('.icon').fadeTo('slow', 0.5);
            });
            function openDialog() {
                $("#status_image_selector").click();
            }

            var share_group_id = <?php
if (is_int($feed_id)) {
    echo $feed_id;
}
else {
    echo "'$feed_id'";
}
?>;

            $(function($)
            {
                $(document).on('click', '.home_like_icon', function() {
                    if ($(this).css('opacity') == '1') {
                        $(this).css('opacity', '0.3');
                    } else {
                        $(this).css('opacity', '1');
                    }
                });

                $("#post_file").change(function(e)
                {
                    uploadFile('file', '#post_file', 'addToStatus');
                });
                
                $('#status_text').focus(function() {
                    $(this).css('min-height', '100px');
                    $('#file_share').show();
                    $('#post_more_options').show();
                    $('.post_wrapper').css('padding-bottom', $('.post_more_options').height());
                    $('.post_media_wrapper').show();
                });
                
                $('#status_text').on('input', function(){
                    $(this).css('height', '0px');
                    $(this).css('height', $(this)[0].scrollHeight+"px");
                });

                $('.default_dropdown_item').click(function()
                {
                    $('div[wrapper_id="' + $(this).attr('controller_id') + '"]').find('.default_dropdown_preview').text($(this).text());
                    share_group_id = $(this).attr('share_id');
                    //console.log(share_group_id);
                });
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
        <div class="container_home" id="home_container">
            <div class='home_feed_post_container' style="padding-top:20px;">
                <div class='post_wrapper'>
                    <table style='width:100%;' cellspacing='0' cellpadding='0'><tr><td>
                        <table style='width:100%;' cellspacing='0' cellpadding='0'><tr style='height:100%;'><td>
                            <textarea tabindex='1' id="status_text" placeholder= "Update Status..." class="status_text scroll_thin"></textarea>
                                </td></tr><tr><td class='post_content_wrapper'>
                                    <div class="post_media_wrapper">
                                        <div class='post_media_wrapper_background user_preview_name'>Attach Files to Dropbox &#10138;</div>
                                        <img class='post_media_loader' src='Images/ajax-loader.gif'></img>
                                    </div>
                        </td></tr></table>
                    </td><td style='width:200px;height:100%;position: relative;'>
                    <div id='file_share' class='scroll_thin'>
                        <table id='file_dialog' style='width:100%;' cellspacing="0" cellpadding="0">
                            <?php
                            foreach ($files->getList_r() as $file) {
                                $home->fileList($file);
                            }
                            ?>
                        </table>
                    </div>
                    </td></tr></table>
                    <div id='post_more_options' class='post_more_options'>
                        <button onclick="submitPost();" class="pure-button-success small">POST</button>
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
                                        echo "<li class='default_dropdown_item' controller_id='audience_selector' share_id='" . $single_group . "'>";
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
                <div id='feed_wrapper_scroller' style='width:100%;' class='scroll_thin_horizontal'>
                    <table>
                        <tr>
                            <td>
                                <div id='a' filter_id = 'a' class="feed_selector home_feed_selector 
                                <?php
                                if ($feed_id === 'a') {
                                    echo 'active_feed';
                                }
                                ?>
                                     ">All</div>
                            </td>
                            <td>
                                <div id='s' filter_id = 's' class="feed_selector home_feed_selector 
                                <?php
                                if ($feed_id === 's') {
                                    echo 'active_feed';
                                }
                                ?>
                                     ">
                                <?php echo $system->trimStr($user->getCommunityName(), 15); ?>
                                </div>
                            </td>
                            <td>
                                <div id='y' filter_id = 'y' class="feed_selector home_feed_selector 
                                <?php
                                if ($feed_id === 'y') {
                                    echo 'active_feed';
                                }
                                ?>
                                     ">Year <?php echo $user->getPosition(); ?>
                                </div>
                            </td>
                            <?php
                            foreach ($group->getUserGroups() as $single_group) {
                                echo '<td id="feed_wrapper_scroller"><div style="border-bottom:3px solid blue;" id="' . $single_group . '" filter_id = "' . $single_group . '" class="feed_selector home_feed_selector ' . ($feed_id == $single_group ? "active_feed" : "") . '">' . $system->trimStr($group->getGroupName($single_group), 15) . '</div></td>';
                            }
                            ?>
                        </tr>
                    </table>
                </div>
            </div>
            <div id='home_refresh'> 
                <div class='home_feed_container'>
                    <?php
                    $count = $activity_query->rowCount();
                    while ($activity = $activity_query->fetch(PDO::FETCH_ASSOC)) {
                        $home->homeify($activity, $database_connection, $user);
                    }

                    if ($count == 0) {
                        echo "<hr><center><span style='font-family: century gothic;'>You have no notifications in this Live Feed!</span></center>";
                    }
                    else {
                        echo '</div>'; // close container div
                    }
                    ?>	
                </div>
            </div>
            <script>
                function showhide(element)
                {
                    $(element).toggle("slide");
                }
            </script>
            <script>

                $('.home_feed_selector').click(function(event)
                {
                    $('#home_refresh').fadeOut(100, function()
                    {
                        $(this).empty();
                        $('#home_refresh').append('<center><img style="margin-top:50px;" src="Images/ajax-loader.gif"></img></center>');
                        $(this).fadeIn();
                    });
                    $
                    $('.home_feed_selector').removeClass('active_feed');
                    $(this).addClass('active_feed');

                    var element_id = "#" + $(this).attr('id');
                    var wrapper = "#" + $(this).parents('div[id]').attr('id');

                    scrollH(element_id, wrapper, 400);

                    var value = $(this).attr('filter_id');
                    if (typeof value === "undefined")
                    {

                    }
                    else
                    {
                        if (isNaN(value))
                        {
                            if (value == 'a')
                            {
                                setCookie('current_feed', 'a');
                            }
                            else
                            {
                                setCookie('current_feed', value);
                            }
                        }
                        else
                        {
                            setCookie('current_feed', value);
                        }
                    }
                    getHomeContent(value);
                });
                function getHomeContent(feed_id, callback)
                {
                    if (typeof feed_id === "undefined")
                    {

                    }
                    else
                    {
                        if (isNaN(feed_id))
                        {
                            if (feed_id == 'a')
                            {
                                $('#home_refresh').load("home #home_refresh", function(response) {

                                });
                            }
                            else
                            {
                                $('#home_refresh').load("home?f=" + feed_id + " #home_refresh", function(response) {

                                });
                            }
                        }
                        else
                        {
                            $('#home_refresh').load("home?fg=" + feed_id + " #home_refresh", function(response) {

                            });
                        }
                    }
                    callback();
                }
            </script>
    </body>
</html>