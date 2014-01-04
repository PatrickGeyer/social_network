<?php
include_once('Scripts/lock.php');
$user = new User;
if (isset($_GET['id'])) {
    $userid = urldecode(base64_decode($_GET['id']));
    if ($userid == $user->getId()) {
        $page_identifier = 'user';
    }
}


if (isset($_GET['f']) && $_GET['f'] == 'f') {
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
            dialog("Invite",
                    {
                        type: "text",
                        content: "Invite <em><?php echo $user->getName($userid); ?></em> to join the group <em>" + group + "</em>.</p>",
                    },
                    {
                        type: "success",
                        text: "Invite",
                        onclick: "inviteUser(" + id + ", " + group_id + ");dialogLoad();"
                    },
            {
                modal: false
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
    <div class="container">
        <table class='info_table_layout'>
            <tr>
                <td rowspan='2' style='width:220px;'>
                    <div class='profilepicturediv' style='background-image:url("<?php echo $user->getProfilePicture('thumb', $userid); ?>");' 
                         onclick='initiateTheater("
                         <?php
                         echo $user->getProfilePicture('original', $userid);
                         ?> ", "no_text");
                                 adjustTheater("no_text");'>
                             <?php
                             if ($user->getId() == $userid) {
                                 echo "<div class='profile_picture_upload'>"
                                 . "<table style='height:100%;'>"
                                 . "<tr style='vertical-align:middle;'>"
                                 . "<td>"
                                 . "<span class='profile_picture_upload' onclick='show_photo_upload();'>Upload</span>"
                                 . "</td>"
                                 . "</tr>"
                                 . "</table>"
                                 . "</div>";
                             }
                             ?>
                    </div>
                </td>
                <td>
                    <div class="pseudonym">
                        <p class='name_title'><?php echo $user->getName($userid); ?></p>
                        <div style="padding-bottom:5px;">
                            <a href="community?id=<?php echo urlencode(base64_encode($user->getCommunityId($userid))); ?>">
                                <span class='user_preview_community'><?php echo $user->getCommunityName($userid); ?></span>
                                <span class='user_preview_position'> &bull; <?php echo $user->getPosition($userid); ?></span>
                            </a>
                        </div>
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
                        . "style='position:absolute; right:10px; top:15px;' "
                        . "class='pure-button-success small'>Manage</button>";
                    }

                    if ($userid != $user->getId()) {
                        echo "<div style='' wrapper_id='invite_selector' class='default_dropdown_selector' onclick='$(&#39;#group_invites&#39;).slideToggle(&#39;fast&#39;);'>
						Invite to Group";
                        echo "<div id='invite_selector' class='default_dropdown_wrapper' style='display:none;float:right;'>";
                        echo "<ul class='default_dropdown_menu'>";
                        foreach ($group->getUserGroups() as $users_group) {
                            $query1 = "SELECT group_id FROM group_member WHERE member_id = :user_id AND group_id = :group_id;";
                            $query1 = $database_connection->prepare($query1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                            $query1->execute(array(":user_id" => $userid, "group_id" => $users_group));
                            $query1 = $query1->fetchColumn();

                            if ($query1 == "") {
                                echo "<script name='text_append'>$('#invite_text_holder').show();</script>";
                                $query_group1 = "SELECT * FROM `group` WHERE id = :group_id AND allow_member_invite = 1;";
                                $query_group = $database_connection->prepare($query_group1, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                                $query_group->execute(array(":group_id" => $users_group));
                                $group_info = $query_group->fetch(PDO::FETCH_ASSOC);
                                echo "<li class='default_dropdown_item' "
                                . "onclick='showInvite(\"" . $group_info['group_name']
                                . "\", " . $userid . ", " . $group_info['id'] . ");'>"
                                . $group_info['group_name'] . "</li>";
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
        <div id='feed_wrapper_scroller' style='padding-left:20px;border-top:1px solid lightgrey;border-bottom:1px solid lightgrey;'>
            <table cellspacing='0'>
                <tr>
                    <td style='margin-right:5px;'>
                        <div id='a' filter_id = 'p' class="feed_selector user_feed_selector <?php
                        if ($feed_id == 'p') {
                            echo 'active_feed';
                        }
                        ?>">Posts</div>
                    </td>
                    <td>
                        <div id='s' filter_id = 'f' class="feed_selector user_feed_selector <?php
                        if ($feed_id == 'f') {
                            echo 'active_feed';
                        }
                        ?>">Files</div>
                    </td>
                </tr>
            </table>
        </div>
        <div id='user_refresh'>
            <div style='' id="user_activity">
                <?php
                if ($feed_id == "p") {
                    $array = $user->getActivity($userid);
                    $count = count($array);
                    foreach ($array as $activity) {
                        $home->homeify($activity, $database_connection, $user);
                    }
                }
                else {
                    echo "<div id='main_file' class='file' style='border-bottom:1px solid lightblue;'>";
                    foreach ($files->getSharedList($userid, $user->getId()) as $file) {
                        $files->tableSort($file, false, true, $userid);
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div id='invite_text' style='display:none;'></div>
</body>
<script>
    $('.user_feed_selector').click(function(event)
    {
        $('.user_feed_selector').removeClass('active_feed');
        $(this).addClass('active_feed');

        $('#user_refresh').fadeOut(100, function()
        {
            $(this).empty();
            $('#user_refresh').append('<center><img style="margin-top:50px;" src="Images/ajax-loader.gif"></img></center>');
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
            setCookie('user_feed', value);
        }
        getUserContent(value);
    });
    function getUserContent(feed_id)
    {
        var encrypted_id = "<?php echo $_GET['id']; ?>";
        $('#user_refresh').load("user?id=" + encrypted_id + "&f=" + feed_id + " #user_refresh", function(response) {
        });
    }
</script>