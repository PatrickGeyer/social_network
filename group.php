<?php

function print_body() {
    require_once 'Thumbnail/ThumbLib.inc.php';

    $group_id = urldecode(base64_decode($_GET['id']));

    $leader_query = "SELECT id, name, position FROM user WHERE id = " . Registry::get('group')->getFounderId($group_id) . "";
    $leader_query = Registry::get('db')->prepare($leader_query);
    $leader_query->execute();
    $leader = $leader_query->fetch();


    $is_member = Registry::get('group')->isMember(Registry::get('user')->getId(), $group_id);

    $activity_query = "SELECT id, user_id, status_text, type, time FROM activity WHERE 
id IN (SELECT activity_id FROM activity_share WHERE group_id = :group_id  AND direct = true)
ORDER BY time DESC";
    $activity_query = Registry::get('db')->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $activity_query->execute(array(":group_id" => $group_id));


    if (isset($_GET['f']) && $_GET['f'] == 'f') {
        $feed_id = 'f';
    }
    else {
        $feed_id = 'p';
    }
    ?>
    <head>
        <link rel="stylesheet" type="text/css" href="CSS/home.css">
        <link rel="stylesheet" type="text/css" href="CSS/user.css">
        <title>Group - <?php echo Registry::get('group')->getName($group_id); ?></title>
        <script>
            var receivers = [];
            function sendMessage()
            {
                var reply = false;
                var title = $('#title').val();
                var message = $('#1message').val();
                $.post("Scripts/sendmessage.php", {title: title, message: message, receivers: receivers, reply: reply}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("message?thread=" + status[1]);
                    }
                });
            }
            function leaveGroup(group_id)
            {
                $.post("Scripts/group.class.php", {action: "leave", group_id: group_id}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("group?id=" + status[1]);
                    }
                });
            }
            function deleteGroup(group_id)
            {
                $.post("Scripts/group.class.php", {action: "deleteG", group_id: group_id}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("home");
                    }
                });
            }
            function abdicateGroup(group_id)
            {
                $.post("Scripts/group.class.php.php", {action: "abdicate", group_id: group_id}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("home");
                    }
                });
            }
            function toggleActions()
            {
                $('.action').slideToggle();
            }
        </script>
    </head>
    <div class="container contentblock">
        <table class='info_table_layout'>
            <tr>
                <td style='width:220px;'>
                    <div class='profilepicturediv' style='background-image:url("<?php echo Registry::get('group')->getProfilePicture('thumb', $group_id); ?>");'>
                        <?php
//if(Registry::get('user')->getId() == Registry::get('group')->getFounder())
// {
// 	echo "
// <div class='profile_picture_upload'>
// 	<table style='height:100%;'>
// 		<tr style='vertical-align:middle;'>
// 			<td>
// 				<span class='profile_picture_upload' onclick='show_photo_upload();'>Upload</span>
// 			</td>
// 		</tr>
// 	</table>
// </div>";
//}
                        ?>
                    </div>
                </td>
                <td>
                    <div class="pseudonym">
                        <p class='name_title'><?php echo Registry::get('group')->getName($group_id); ?></p>
                        <?php
                        if (Registry::get('group')->getAbout($group_id) != "") {
                            echo "<div style='margin-top:10px;'>
							<img id='about_saved' style='display:none;' title='Saved' src='Images\Icons\icons/tick-circle.png'></img>
							</div>";
                            echo "<div ";
                            //if(Registry::get('user')id == Registry::get('user')->getId())
                            //{
                            //	echo "title='Click to Edit' contenteditable ";
                            //}
                            echo " id='about_edit_show' style='font-size:13px;padding:3px;width:100%;'>" . Registry::get('group')->getAbout($group_id);
                            echo "</div>";
                        }
                        ?>
                    </div>
                </td>
                <td>
                    <?php
                    // if(Registry::get('user')id == Registry::get('user')->getId())
                    // {
                    // 	echo "<button onclick='window.location.assign(&quot;settings&quot;);' style='position:absolute; right:10px; top:15px;' class='pure-button-green small'>Manage</button>";
                    // }
                    // if(Registry::get('user')id != Registry::get('user')->getId())
                    // {
                    // 	echo "<div style='' wrapper_id='invite_selector' class='default_dropdown_selector' onclick='$(&#39;#group_invites&#39;).slideToggle(&#39;fast&#39;);'>
                    // 	Invite to Group";
                    // 	echo "<div id='invite_selector' class='default_dropdown_wrapper' style='display:none;float:right;'>";
                    // 	echo "<ul class='default_dropdown_menu'>";
                    // 	foreach(Registry::get('group')->getUserGroups() as Registry::get('user')s_group)
                    // 	{
                    // 		$query1 = "SELECT group_id FROM group_member WHERE member_id = :user_id AND group_id = :group_id;";
                    // 		$query1 = Registry::get('db')->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    // 		$query1->execute(array(":user_id" => Registry::get('user')id, "group_id" => Registry::get('user')s_group));
                    // 		$query1 = $query1->fetchColumn();
                    // 		if($query1 == "")
                    // 		{
                    // 			echo "<script name='text_append'>$('#invite_text_holder').show();</script>";
                    // 			$query_group1 = "SELECT * FROM `group` WHERE id = :group_id AND allow_member_invite = 1;";
                    // 			$query_group = Registry::get('db')->prepare($query_group1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    // 			$query_group->execute(array(":group_id" => Registry::get('user')s_group));
                    // 			$group_info = $query_group->fetch(PDO::FETCH_ASSOC);
                    // 			echo "<li class='default_dropdown_item'
                    // 			onclick='showInvite(\"".$group_info['group_name']."\", ".Registry::get('user')id.", ".$group_info['id'].");'
                    // 			>".$group_info['group_name']."</li>";
                    // 		}
                    // 	}
                    // 	echo "</ul>";
                    // 	echo "</div>";
                    // }
                    ?>
                </td>
            </tr>
        </table>
        <div style='float:right;'>
            <?php
            if (Registry::get('group')->getFounderId($group_id) == Registry::get('user')->getId()) {
                echo '<button class="pure-button-error action small" onclick="deleteGroup(' . $group_id . ');">Delete Group</button>';
                echo '<button class="pure-button-green action small" onclick="editGroup(' . $group_id . ');">Edit</button>';
            }

            if ($is_member == true) {
                echo '<button class="pure-button-blue action small" onclick="leaveGroup(' . $group_id . ');">Leave Group</button>';
            }
            ?>
        </div>
        <div id='feed_wrapper_scroller' style='padding-left:20px;border-top:1px solid lightgrey;border-bottom:1px solid lightgrey;'>
            <table cellspacing='0'>
                <tr>
                    <td style='margin-right:5px;'>
                        <div id='a' filter_id = 'p' class="feed_selector group_feed_selector <?php
                             if ($feed_id == 'p') {
                                 echo 'active_feed';
                             }
                             ?>">Posts</div>
                    </td>
                    <td>
                        <div id='s' filter_id = 'f' class="feed_selector group_feed_selector <?php
                             if ($feed_id == 'f') {
                                 echo 'active_feed';
                             }
                             ?>">Files</div>
                    </td>
                </tr>
            </table>
        </div>
        <div id='container_refresh'>
            <div style='' id="group_activity">
                <?php
                if ($feed_id == "p") {
                    $array = $activity_query->fetchAll(PDO::FETCH_ASSOC);
                    $count = count($array);
                    foreach ($array as $activity) {
                        Registry::get('home')->homeify($activity, Registry::get('db'), Registry::get('user'));
                    }
                }
                else {
                    echo "<div id='main_file' class='file' style='border-bottom:1px solid lightblue;'>";
                    foreach (Registry::get('files')->getSharedList($group_id, Registry::get('user')->getId(), "group") as $file) {
                        Registry::get('files')->tableSort($file, false, true, Registry::get('user')->user_id);
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        $('.group_feed_selector').click(function(event)
        {
            $('.group_feed_selector').removeClass('active_feed');
            $(this).addClass('active_feed');

            $('#container_refresh').fadeOut(100, function()
            {
                $(this).empty();
                $('#container_refresh').append('<center><img style="margin-top:50px;" src="Images/ajax-loader.gif"></img></center>');
                $(this).fadeIn();
            });

            var element_id = "#" + $(this).attr('id');
            var wrapper = "#" + $(this).parents('div[id]').attr('id');

            scrollH(element_id, wrapper, 400);

            var value = $(this).attr('filter_id');
            if (typeof value === "undefined")
            {

            }
            else
            {
                setCookie('group_feed', value);
            }
            getUserContent(value);
        });
        function getUserContent(feed_id)
        {
            var encrypted_id = "<?php echo $_GET['id']; ?>";
            $('#container_refresh').load("group?id=" + encrypted_id + "&f=" + feed_id + " #container_refresh", function(response) {
            });
        }
    </script>
<?php
}

require 'Scripts/lock.php';
?>