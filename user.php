<?php
include_once('Scripts/lock.php');
$user = new User;
if (isset($_GET['id'])) {
    $userid = urldecode(base64_decode($_GET['id']));
    if ($userid == $user->getId()) {
        $page_identifier = 'user';
    }
} else {
    $userid = $user->user_id;
}


if (isset($_GET['tab']) && $_GET['tab'] == 'f') {
    $feed_id = 'f';
}
else {
    $feed_id = 'p';
}

include_once('welcome.php');
include_once('chat.php');
?>
<head>
    <title><?php echo $name = $user->getName($userid); ?></title>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBtmd6SX8JrdtTWhuVqIA37XJPO2nwtM6g&sensor=true"></script>

    <script>
        function showInvite(group, id, group_id)
        {
            dialog(
                    content = {
                        type: "text",
                        content: "Invite <em><?php echo $user->getName($userid); ?></em> to join the group <em>" + group + "</em>.</p>",
                    },
                    buttons = [{
                            type: "success",
                            text: "Invite",
                            onclick: "inviteUser(" + id + ", " + group_id + ");dialogLoad();"
                        }],
            properties = {
                modal: false,
                title: "Invite"
            });
        }
        function inviteUser(id, group_id)
        {
            $.post("Scripts/group_actions.php", {action: "invite", user_id: id, group_id: group_id}, function(response)
            {
                if (("success").indexOf(response))
                {
                    removeDialog();
                }
                else
                {
                    alert(response);
                }
            });
        }
        $(function()
        {
        	<?php
        	if ($feed_id == "p") :?>
               getFeed(<?php echo $userid; ?>, 'user', min_activity_id, null, function(response){
                    			var string = '';
                    			for (var i in response) {
                        			string +=  Application.prototype.feed.homify(response[i]);
                    			}
                    			if(response.length == 0) {
                    				$('.feed_container').prepend(empty_feed({text:"This user has not made any posts!"}));
                    			} else {
                    				$('.feed_container').prepend(string);
                    			}
                			});
            <?php endif; ?>
            $('#about_edit_show').mouseenter(function() {
                $('#profile_about_edit').show();
            }).mouseleave(
                    function() {
                        $('#profile_about_edit').hide();
                    });

            $('.profilepicture').mouseenter(function() {
                $('#profile_picture_edit').show();
            }).mouseleave(
                    function() {
                        $('#profile_picture_edit').hide();
                    });
            $('#about_edit_show').blur(function()
            {
                submitData();
            });

            $("#about_edit_show").focusin(function() {
                $("#about_edit_show").css("background", "white");
            });
            $("#about_edit_show").focusout(function() {
                $("#about_edit_show").css("background", "");
            });
            createMap('<?php echo $user->getLocation($userid)['country']; ?>', '<?php echo $user->getLocation($userid)['city']; ?>');
        });
        function submitData()
        {
            var about = $('#about_edit_show').html();
            var email = '';
            var school = '';
            var year = '';
            $.post('Scripts/user.class.php', {about: about, email: email, year: year}, function(response)
            {
                $('#about_saved').fadeIn(function()
                {
                    $('#about_saved').fadeOut(1000);
                });
                //alert(response);
            });
        }
    </script>
</head>
<body>
    <div class="global_container">
        <?php include_once('left_bar.php'); ?>
        <div class="container">
            <table class='info_table_layout'>
                <tr>
                    <td rowspan='2' style='width:220px;'>
                        <div class='profilepicturediv' style='background-image:url("<?php echo $user->getProfilePicture('THUMB', $userid); ?>");'>
                            <?php
                            if ($user->getId() == $userid) {
                                echo "<div class='profile_picture_upload'>"
                                . "<table style='height:100%;width:100%;'>"
                                . "<tr style='vertical-align:middle;'>"
                                . "<td style='text-align:center;'>"
                                . "<button onclick='show_photo_choose();' class='profile_picture_upload pure-button-primary small'>Upload</button>"
                                . "</td>"
                                . "</tr>"
                                . "</table>"
                                . "</div>";
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <div class="pseudonym" entity_type='user' entity_id='<?php echo $userid; ?>'>
                            <p class='name_title'><?php echo $user->getName($userid); ?></p>
                            <?php
                            if ($user->getAbout($userid) != "") {
                                echo "<div style='margin-top:10px;'>"
                                . "<img id='about_saved' style='display:none;' title='Saved' src='Images\Icons\icons/tick-circle.png'></img>"
                                . "</div>";
                                echo "<div ";
                                if ($userid == $user->getId()) {
                                    echo "title='Click to Edit' contenteditable ";
                                }
                                echo " id='about_edit_show' style='font-size:13px;padding:3px;width:100%;'>" . $user->getAbout($userid);
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        if ($userid == $user->getId()) {
                            echo "<button onclick='window.location.assign(&quot;settings&quot;);' "
                            . "style='float:right;' "
                            . "class='pure-button-primary'>Manage</button><br />";
                            echo "<button onclick='window.location.assign(&quot;stats&quot;);' "
                            . "style='float:right;' "
                            . "class='pure-button-primary'>Stats</button>";
                        }

                        if ($userid != $user->getId()) {
                            echo "<button style='float:right;' class='pure-button-primary connect_button'>Connect</button><br />";
                            echo "<div style='background-image:none;padding-right:0px;float:right;' wrapper_id='invite_selector' class='default_dropdown_actions'>
				<button class='pure-button-primary connect_button'>Invite</button>";
                            echo "<div id='invite_selector' class='default_dropdown_wrapper' style='display:none;float:right;'>";
                            echo "<ul class='default_dropdown_menu'>";
                            foreach ($group->getUserGroups() as $users_group) {
                                $query1 = "SELECT group_id FROM group_member WHERE user_id = :user_id AND group_id = :group_id;";
                                $query1 = $database_connection->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                                $query1->execute(array(":user_id" => $userid, "group_id" => $users_group));
                                $query1 = $query1->fetchColumn();

                                if ($query1 == "") {
                                    $name = $group->getGroupName($users_group);
                                    echo "<script name='text_append'>$('#invite_text_holder').show();</script>";
                                    echo "<li class='default_dropdown_item' "
                                    . "onclick='showInvite(\"" . $name
                                    . "\", " . $userid . ", " . $users_group . ");'>"
                                    . $name . "</li>";
                                }
                            }
                            echo "</ul>";
                            echo "</div>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style='position:relative;'>
                        <div style='display:none;' id='more_user'>
                            <input type='text' value='<?php echo $user->getEmail($userid); ?>' />
                        </div>
                        <div id='map-canvas' class='map_container'>
                        </div>
                    </td>
                </tr>
            </table>
            <div class='feed_wrapper_scroller'>
                <table cellspacing='0'>
                    <tr>
                        <td style='margin-right:5px;'>
                            <a href='user?id=<?php echo $_GET['id']; ?>&tab=p'><div id='a' feed_id='user' filter_id = 'u_<?php echo $userid; ?>' class="feed_selector user_feed_selector <?php
                            if ($feed_id == 'p') {
                                echo 'active_feed';
                            }
                            ?>">Posts</div></a>
                        </td>
                        <td>
                            <a href='user?id=<?php echo $_GET['id']; ?>&tab=f'><div id='s' feed_id='user' action='user_files' filter_id = 'f' class="feed_selector user_feed_selector <?php
                            if ($feed_id == 'f') {
                                echo 'active_feed';
                            }
                            ?>">Files</div></a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id='user_refresh'>
                <div class='feed_container' id="feed_refresh">
                    <?php
                    if ($feed_id == "p") {
                    	
                    }
                    else {
                        echo "<div id='main_file' class='file post_height_restrictor' style='border-bottom:1px solid lightblue;'>";
                        foreach ($files->getSharedList($userid, $user->getId()) as $file) {
                            $files->tableSort($file, false, true, $userid);
                        }
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            <div id='invite_text' style='display:none;'></div>
        </div>
        <?php include_once 'right_bar.php'; ?>
    </div>
</body>