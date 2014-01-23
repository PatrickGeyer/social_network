<?php
include_once('Scripts/lock.php');
include_once('Scripts/js.php');
if (!isset($page_identifier)) {
    $page_identifier = "none_set";
}
?>
<!DOCTYPE HTML>
<head>
    <script>
        $(document).click(function(e)
        {
            $("#names_universal").hide();
        });	// ***ADD*** if element is input do nothing!
        getNotificationNumber();
        function getNotificationNumber() {
            $.post('Scripts/notifications.class.php', {action:"alert_num"}, function(response){
                response = JSON.parse(response);
                if(response.message != '0') {
                    $('#message_num').text(response.message);
                    $('#message_num').show();
                }
                else {
                    $('#message_num').hide();
                }
                if(response.notification != '0') {
                    $('#notification_num').text(response.notification);
                    $('#notification_num').show();
                }
                else {
                    $('#notification_num').hide();
                }
                if(response.network != '0') {
                    $('#network_num').text(response.network);
                    $('#network_num').show();
                }
                else {
                    $('#network_num').hide();
                }
                setTimeout(getNotificationNumber, 10000);
            });
        }
        var invited_members = [];
        function addreceiver(new_receiver, new_receiver_name)
        {
            var found = $.inArray(new_receiver, invited_members);
            if (found != -1)
            {

            }
            else
            {
                invited_members.push(new_receiver);
                $('#names_input').val("");
                var html = "<td style='min-width:100px;' id = '" + new_receiver + "'> \
                    <div class='tag-triangle'></div><div class='added_name'><span style='font-family:century gothic;'>" + new_receiver_name + "</span> \
                    <span class='delete_receiver' onclick='removereceiver(" + new_receiver + ");'>x \
                    </span></div></td>";
                $('#names_slot').before(html);
            }
        }
        function removereceiver(receiver_id)
        {
            var index = invited_members.indexOf(receiver_id);
            if (index > -1)
            {
                invited_members.splice(index, 1);
            }
            $('#' + receiver_id).remove();
        }
        function createGroup()
        {
            var group_name = $('#group_name').val();
            var group_about = $('#group_about').val();
            var group_type = $('#group_type').val();
            $.post("creategroup.php", {group_name: group_name, group_about: group_about, group_type: group_type, invited_members: invited_members}, function(response)
            {
                var status = response.split("/");
                if (status[0] == "success")
                {
                    window.location.replace("group?id=" + status[1]);
                }
            });
        }
    </script>

    <script>
        $(function()
        {
            $(document).on('click', "#current_page", function(event)
            {
                event.stopPropagation();
                $(".general").slideDown("fast");
                $(".personal").slideUp("fast");
                $("#messagediv").slideUp("fast");
                $("#notificationdiv").slideUp("fast");
                $("#networkdiv").slideUp("fast");
                $("#geardiv").slideUp("fast");
            });
            $(document).on('click', "#home_icon", function(event)
            {
                event.stopPropagation();
                window.location.replace("home");
                $("#notificationdiv").slideUp("fast");
                $("#networkdiv").slideUp("fast");
                $("#geardiv").slideUp("fast");
            });
            $(document).on('click', "#personal", function(event)
            {
                event.stopPropagation();
                $(".personal").slideDown("fast");
                $(".general").slideUp("fast");
                $("#messagediv").slideUp("fast");
                $("#notificationdiv").slideUp("fast");
                $("#networkdiv").slideUp("fast");
                $("#geardiv").slideUp("fast");
            });
            $(document).on('click', "#message_click", function(event)
            {
                markAllSeen('message');
                event.stopPropagation();
                $("#messagediv").slideDown("fast");
                $(".personal").slideUp("fast");
                $(".general").slideUp("fast");
                $("#notificationdiv").slideUp("fast");
                $("#networkdiv").slideUp("fast");
                $("#geardiv").slideUp("fast");
                $('.message_notification').hide();
            });
            $(document).on('click', "#notification_click", function(event)
            {
                markAllSeen('notification');
                event.stopPropagation();
                $("#notificationdiv").slideDown("fast");
                $(".personal").slideUp("fast");
                $(".general").slideUp("fast");
                $("#messagediv").slideUp("fast");
                $("#networkdiv").slideUp("fast");
                $("#geardiv").slideUp("fast");
                $('#notification_counter').hide();
            });
            $(document).on('click', "#network_click", function(event)
            {
                markAllSeen('network');
                event.stopPropagation();
                $("#notificationdiv").slideUp("fast");
                $(".personal").slideUp("fast");
                $(".general").slideUp("fast");
                $("#messagediv").slideUp("fast");
                $("#networkdiv").slideDown("fast");
                $("#geardiv").slideUp("fast");
                $('#network_counter').hide();
            });
            $(document).on('click', "#gear_click", function(event)
            {
                event.stopPropagation();
                $("#notificationdiv").slideUp("fast");
                $(".personal").slideUp("fast");
                $(".general").slideUp("fast");
                $("#messagediv").slideUp("fast");
                $("#geardiv").slideDown("fast");
            });
            $(document).on('click', "html", function()
            {
                $(".general").slideUp("fast");
                $(".personal").slideUp("fast");
                $("#messagediv").slideUp("fast");
                $("#notificationdiv").slideUp("fast");
                $("#networkdiv").slideUp("fast");
                $("#geardiv").slideUp("fast");
            });
        });
        function markAllSeen(type)
        {
            $.post("Scripts/notifications.class.php", {action: "mark", type: type}, function(response){});
        }
        function markNotificationRead(id, nextPage)
        {
            $.post("Scripts/notifications.class.php", {action: "markNotificationRead", id: id}, function(response)
            {
                window.location.assign(nextPage);
            });
        }

        $(function()
        {
            if (document.title != "")
            {
                $('#current_page_link').prepend(document.title);
            }
            else
            {
                $('#current_page_link').prepend("Page");
            }
        });
    </script>
</head>
<body>
    <div class="headerbar">
        <div id="refresh">
            <div class="container_headerbar">
                <span style='cursor:pointer;margin-left:-200px;color:white;font-size:1.8em;' onclick='window.location.assign("home");'>Collaborator</span>
                <div style="position:absolute;right:500px;top:0;">
                    <div class="message" id="message_click">
                        <img id="message" class ="message" src='<?php echo System::INBOX_IMG; ?>'></img>
                        <div id="messagediv" class="popup_div">
                            <div class="popup_top">
                                <span class='popup_header'>Messages</span>
                                <a href="message" class='user_preview_name' style='top:2px;font-size:12px;position:absolute;right:10px;'>- Compose</a>
                            </div>
                            <div class="popup_content scroll_medium">
                                <ul class="message"> 
                                    <?php
                                    $message_count = $notification->getMessageNum();
                                    $messages = $notification->getMessage();
                                    foreach ($messages as $message) {
                                        $participants = $notification->getReceivers($message['thread'], 'list');
                                        $picture = $user->getProfilePicture('chat', $message['sender_id']);
                                        //$names = $notification->styleReceiverList($participants, 'list');
                                        echo "<li class='";
                                        if ($message['read'] == 0) {
                                            echo "messageunread";
                                        }
                                        else {
                                            echo "message";
                                        }
                                        echo "'><a class='message' href='message?thread=" . $message['thread'] . "&id=" . $message['id'] . "'>"
                                        . "<div style='display:table-row;'>"
                                        . "<div class='notification_user_image'>"
                                        . $notification->getMessagePicture(NULL, $message['thread'])
                                        . "</div><div style='display:table-cell;vertical-align:top;'>"
                                        . "<p class='ellipsis_overflow notification_name'>" . $participants . "</p>"
                                        . "<p class='ellipsis_overflow notification_info'>" . $message['message']
                                        . "</p></div></div></a></li> ";
                                    }
                                    $total_message_count = count($messages);
                                    if ($total_message_count == 0) {
                                        echo "<div style='padding:5px;color:grey;'>No Messages</div>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <span id='message_num' class="message_notification"></span>
                    </div>
                    <div class="notification" id="notification_click">
                        <img style='height:18px;' id="notification" class ="message" src='<?php echo System::NOTIFICATION_IMG; ?>'></img><br>
                        <div id="notificationdiv" class="popup_div">
                            <div class="popup_top">
                                <span class='popup_header'>Notifications</span>
                            </div>
                            <div class="popup_content scroll_medium">
                                <ul class="notify"> 
                                    <?php
                                    $notify_count = $notification->getNotificationNum();
                                    $notifications = $notification->getNotification();
                                    foreach ($notifications as $notify) {
                                        $picture = $user->getProfilePicture("chat", $notify['sender_id']);
                                        $name = $user->getName($notify['sender_id']);
                                        echo "<li onclick='markNotificationRead(" . $notify['id'] . ", \"post?id=" . $notify['post_id'] . "\");' class='";
                                        if ($notify['read'] == 0) {
                                            echo "messageunread";
                                        }
                                        else {
                                            echo "message";
                                        }

                                        if ($notify['type'] == 'like' || $notify['type'] == 'dislike') {
                                            echo "'><div style='display:table-row;'>"
                                            . "<img class='notification_user_image' src='" . $picture . "'></img>"
                                            . "<p style='vertical-align:top; display:table-cell;'><b>" . $name . "</b> liked on your post</p></div></li> ";
                                        }
                                        else if($notify['type'] == 'comment_like') {
                                            echo "'><div style='display:table-row;'>"
                                            . "<img class='notification_user_image' src='" . $picture . "'></img>"
                                            . "<p style='vertical-align:top; display:table-cell;'><b>" . $name . "</b> liked your comment</p></div></li> ";
                                        }
                                    }
                                    $total_notify_count = count($notifications);
                                    if ($total_notify_count == 0) {
                                        echo "<div style='padding:5px;color:grey;'>No Notifications</div>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <span id='notification_num' class="message_notification"></span>
                    </div>
                    <div class="network" id="network_click">
                        <img id="network" class="message network" src='<?php echo System::NETWORK_IMG; ?>'></img><br>
                        <div id="networkdiv" class="popup_div">
                            <div class="popup_top">
                                <span class='popup_header'>Network</span>
                            </div>
                            <div class="popup_content scroll_medium">
                                <ul class="notify"> 
                                    <?php
                                    $network_count = $notification->getNetworkNum();
                                    $networks = $notification->getNetwork();
                                    $total_network_count = count($networks);
                                    if ($total_network_count == 0) {
                                        echo "<div style='padding:5px;color:grey;'>No Network Notifications</div>";
                                    }
                                    foreach ($networks as $network) {
                                        $picture = $user->getProfilePicture("chat", $network['inviter_id']);
                                        $group_name = $group->getGroupName($network['group_id']);
                                        $group_id = $network['group_id'];

                                        echo "<li class='";
                                        if ($network['read'] == 0) {
                                            echo "messageunread";
                                        }
                                        else {
                                            echo "message";
                                        }
                                        echo "'><table><tr style='vertical-align:top;'>"
                                        . "<td style='min-width:40px;'>"
                                        . "<img class='notification_user_image' src='" . $picture . "'></img>"
                                        . "</td><td>"
                                        . "<p style='margin:0;text-align:left;font-size:13px;'>"
                                        . str_replace('$group', '"' . $group_name . '"', str_replace('$user', $user->getName($network['inviter_id']), $phrase->get('group_invite', 'en')))
                                        . "</p></td><td><table cellspacing='0' cellpadding='0'><tr><td>";
                                        if ($network['invite_status'] == 2) {
                                            echo "<button style='margin:0;' onclick='rejectGroup("
                                            . $group_id . ", " . $network['id'] . ");' "
                                            . "class='pure-button-yellow small' id='leave_group_" . $network['id'] . "'>Leave</button>";
                                        }
                                        else if ($network['invite_status'] == 0) {
                                            echo "<button style='margin:0;' onclick='joinGroup("
                                            . $group_id . ", " . $network['id'] . ");' "
                                            . "class='pure-button-primary small' id='join_button_" . $network['id'] . "'>Join</button>";
                                        }
                                        else {
                                            echo "<button style='margin:0;' onclick='joinGroup("
                                            . $group_id . ", " . $network['id'] . ");' "
                                            . "class='pure-button-success small' "
                                            . "id='join_button_" . $network['id'] . "' >Join</button>"
                                            . "</td></tr><tr><td><button onclick='rejectGroup(" . $group_id . ", " . $network['id'] . ");' "
                                            . "class='pure-button-error small' id='reject_button_" . $network['id'] . "'>Reject</button>";
                                            echo "<button style='margin:0;display:none;' onclick='rejectGroup("
                                            . $group_id . ", " . $network['id'] . ");' "
                                            . "class='pure-button-yellow small' id='leave_button_" . $network['id'] . "'>Leave</button>";
                                        }
                                        echo "</td></tr></table></td></tr></table></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <span id='network_num' class="message_notification"></span>
                    </div>
                </div>
                <div style="z-index:11;" class="search">
                    <input class="search_box search_input" autocomplete='off'
                           onkeyup='
                                   search(this.value, "universal", "#names_universal", function() {
                                   });
                                   if (event.keyCode == 13)
                                   {
                                       $("#match").click();
                                   }' type='text' id='names_input' placeholder='Search...' name='receiver'>
                    <div class="search_results" id='names_universal'></div>
                </div>
                <div class="gear">
                    <a style = "cursor:pointer;">
                        <img id="gear_click" style="z-index:11; width:16px; height:16px; " class="logout_image_small message" src ="Images\Icons\Icon_Pacs\Batch-master\Batch-master\PNG\16x16\settings-2.png"></img>
                    </a>
                    <div style="display:none;" id="geardiv" class="geardiv">
                        <ul> 
                            <li class="nav_option"><a title"Logout" href="Scripts/logout.php">Logout</a></li> 
                            <li class="nav_option"><a href="">Privacy</a></li> 
                        </ul> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='left_bar_container'>
                <!-- <img id='logo' style='position:absolute;left:40px;top:4px;max-height:25px;opacity:0.2;cursor:pointer;' onclick='window.location.assign("home");' src='Images/reallogo.png'></img> -->
        <div class="navigation">
            <a href='user?id=<?php echo urlencode(base64_encode($user->getId())); ?>'>
                <div class="user_info 
                <?php
                if ($page_identifier == "user") {
                    echo "current_page_user";
                }
                ?>
                     " style='cursor:pointer; margin-bottom:10px;'>
                    <table cellspacing='0' cellpadding='0'>
                        <tr style='vertical-align:top;'>
                            <td>
                                <div class='welcome_user_profile_picture' style='background-image:url("<?php echo $user->getProfilePicture("chat"); ?>");'></div>
                            </td>
                            <td>
                                <div class='welcome_user_info'>
                                    <span class="current_user_name_edit">
                                        <?php
                                        echo $system->trimStr($user->getName(), 20);
                                        ?>
                                    </span>
                                    <br />
                                    <span class='edit_user_text'>
                                        <?php
                                        echo $system->trimStr($user->getCommunityName(), 15);
                                        ?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </a>
            <hr style='z-index:-1;width:200px;border:0px;border-bottom: 0px solid lightgrey;'>
            <ul class="navigation_list"> 
                <li style='background-image:url("Images/Icons/icons/home.png");' class="nav_option ellipsis_overflow 
                <?php
                if ($page_identifier == "home") {
                    echo "current_page";
                }
                ?>
                    "><a class="nav_option ellipsis_overflow" href="home">Home</a></li> 
                <li style='background-image:url("Images/Icons/icons/paper-plane.png");' class="nav_option 
                <?php
                if ($page_identifier == "school") {
                    echo "current_page";
                }
                ?>
                    "><a class="nav_option ellipsis_overflow" href="community?id=<?php echo urlencode(base64_encode($user->getCommunityId())); ?>"><?php echo $user->getCommunityName(); ?></a></li> 
                <li style='background-image:url("Images/Icons/icons/paper-clip.png");' class="nav_option <?php
                if ($page_identifier == "files") {
                    echo "current_page";
                }
                ?>"><a class="nav_option ellipsis_overflow" href="files">My Files</a></li> 
                <li style='background-image:url("Images/Icons/icons/mail.png");' class="nav_option <?php
                if ($page_identifier == "inbox") {
                    echo "current_page";
                }
                ?>"><a class="nav_option ellipsis_overflow" href="message">Inbox</a></li>
            </ul>
        </div>
        <?php
        if ($page_identifier != "inbox") {
            include_once ("friends_list.php");
        }
        ?>
    </div>
</body>
</html>