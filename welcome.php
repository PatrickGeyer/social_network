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
        function show_group() {
            dialog(
                    content = {
                        type: "html",
                        content: '<div class="pseudonym">\n\
             <table class="none" style="width:100%;">\n\
 <tr>\n\
 <td>\n\
 <input style="border-radius:0; width:100%;" id="group_name_input" type="text" autocomplete="off" placeholder="Group Name" />\n\
 </td>  </tr> <tr> <td>\n\
<textarea style="border-radius:0; width:100%; height:100px;" id = "group_about" class="thin" placeholder="About..."></textarea>\n\
</td> </tr> <tr> <td> <div style="border-radius:0;"> \n\
</div> </td> </tr> </table> \n\
<table class="none" style="width:100%;"> <tr> <td id ="names_slot"></td> </tr><tr><td>\n\
<input autocomplete="off" style="border-radius:0; width:100%;" type="text" id="names_input" class="search_input" placeholder="Add Member..." />\n\
</td> </tr> \n\
</table> \n\
<div class="search_results group_search_results" \n\
style="max-height:100px; position:relative; padding:2px;border: 1px solid lightgrey; background-color:white;" id="names">\n\
 </div> </div>'
                    },
            buttons = [{
                    type: "success",
                    text: "Create",
                    onclick: function() {
                        createGroup();
                        dialogLoad();
                    }
                }],
            properties = {
                modal: false,
                title: "Create a Group"
            });
        }

        var invited_members = [];
        function addGroupReceiver(new_receiver_name, id)
        {
            var html = "<div class='message_added_receiver' id='group_added_receiver_" + id + "'><span>"
                    + new_receiver_name + "</span> \
                    <span class='delete_receiver message_delete_receiver' onclick='removeGroupReceiver(" + id + ");'>x \
                    </span></div>";
            $('#names_slot').before(html);
        }
        function removeGroupReceiver(receiver_id)
        {
            for (var key in invited_members) {
                if (invited_members[key].receiver_id = receiver_id) {
                    invited_members.splice(key, 1);
                    //console.log(invited_members);
                }
            }
            $('#group_added_receiver_' + receiver_id).remove();
        }
        function createGroup()
        {
            var group_name = $('#group_name_input').val();
            if (group_name != "")
            {
                var group_about = $('#group_about').val();
                var group_type = $("#dialog input[type='radio']:checked").val();
                $.post("Scripts/group.class.php", {action: "create", group_name: group_name, group_about: group_about, group_type: group_type, invited_members: invited_members}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("group?id=" + status[1]);
                    }
                });
            }
            else
            {

            }
        }
        $(document).click(function(e)
        {
            $("#names_universal").hide();
        });
        
        function getMessageBox() {
            $.post('Scripts/notifications.class.php', {action: "messageList"}, function(response) {
                $('ul.message').html(response);
                $('#popup_message').height($('ul.message').outerHeight(true));
                $('#popup_message').mCustomScrollbar('update');
            });
        }

        function getNotificationBox() {
            $.post('Scripts/notifications.class.php', {action: "notificationList"}, function(response) {
                $('ul.notify').html(response);
                $('#popup_notify').height($('ul.notify').outerHeight(true));
                $('#popup_notify').mCustomScrollbar('update');
            });
        }

        function getNetworkBox() {
            $.post('Scripts/notifications.class.php', {action: "networkList"}, function(response) {
                $('ul.network').html(response);
                $('#popup_network').height($('ul.network').outerHeight(true));
                $('#popup_network').mCustomScrollbar('update');
            });
        }

        function getNotificationNumber() {
            $.post('Scripts/notifications.class.php', {action:"alert_num"}, function(response){
                response = JSON.parse(response);
                if(response.message != '0') {
                    $('#message_num').text(response.message);
                    $('#message_num').show();
                    getMessageBox();
                }
                else {
                    $('#message_num').hide();
                }
                if(response.notification != '0') {
                    $('#notification_num').text(response.notification);
                    $('#notification_num').show();
                    getNotificationBox();
                }
                else {
                    $('#notification_num').hide();
                }
                if(response.network != '0') {
                    $('#network_num').text(response.network);
                    $('#network_num').show();
                    getNetworkBox();
                }
                else {
                    $('#network_num').hide();
                }

                var all = response.message + response.notification + response.network;
                var title = document.title.lastIndexOf(')');
                if(all > 0) {
                    if(title == -1) {
                        document.title = "(" + all + ") " + document.title;
                    }
                    else {
                        title = document.title.substring(title + 1);
                        document.title = "(" + all + ") " + title;
                    }
                }
                $('#popup_message').mCustomScrollbar('scrollTo', 'top');
                $('#popup_network').mCustomScrollbar('scrollTo', 'top');
                $('#popup_notify').mCustomScrollbar('scrollTo', 'top');
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

        $(function()
        {
            getNotificationNumber();
            var table = "<table style='min-height:100px;height:100px;width:100%;'><tr style='vertical-align:middle;'><td style='width:100%;text-align:center;'><img src='" + AJAX_LOADER + "'></img></td></tr></table>";
            $('ul.message, ul.network, ul.notify').prepend(table);
            
            $('#popup_message').mCustomScrollbar(SCROLL_OPTIONS);
            $('#popup_network').mCustomScrollbar(SCROLL_OPTIONS);
            $('#popup_notify').mCustomScrollbar(SCROLL_OPTIONS);
            
            $(document).on('click', "img.message", function(event)
            {
                $('img.message').removeClass('message_active');
                $(this).addClass('message_active');
            });
            $(document).on('click', "#home_icon", function(event)
            {
                event.stopPropagation();
                window.location.replace("home");
                $("#notificationdiv").hide();
                $("#networkdiv").hide();
                $("#geardiv").hide();
            });
            $(document).on('click', "#personal", function(event)
            {
                event.stopPropagation();
                $(".personal").show();
                $(".general").hide();
                $("#messagediv").hide();
                $("#notificationdiv").hide();
                $("#networkdiv").hide();
                $("#geardiv").hide();
            });
            $(document).on('click', "#message_click", function(event)
            {
                getMessageBox();
                markAllSeen('message');
                event.stopPropagation();
                $("#messagediv").show()
                $(".personal").hide();
                $(".general").hide();
                $("#notificationdiv").hide();
                $("#networkdiv").hide();
                $("#geardiv").hide();
                $('.message_notification').hide();
            });
            $(document).on('click', "#notification_click", function(event)
            {
                getNotificationBox();
                markAllSeen('notification');
                event.stopPropagation();
                $("#notificationdiv").show();
                $(".personal").hide();
                $(".general").hide();
                $("#messagediv").hide();
                $("#networkdiv").hide();
                $("#geardiv").hide();
                $('#notification_counter').hide();
            });
            $(document).on('click', "#network_click", function(event)
            {
                getNetworkBox();
                markAllSeen('network');
                event.stopPropagation();
                $("#notificationdiv").hide();
                $(".personal").hide();
                $(".general").hide();
                $("#messagediv").hide();
                $("#networkdiv").show();
                $("#geardiv").hide();
                $('#network_counter').hide();
            });
            $(document).on('click', "#gear_click", function(event)
            {
                event.stopPropagation();
                $("#notificationdiv").hide();
                $(".personal").hide();
                $(".general").hide();
                $("#messagediv").hide();
                $("#geardiv").show();
            });
            $(document).on('click', "html", function()
            {
                $(".general").hide();
                $(".personal").hide();
                $("#messagediv").hide();
                $("#notificationdiv").hide();
                $("#networkdiv").hide();
                $("#geardiv").hide();
                $('img.message').removeClass('message_active');
            });
            
//            setTimeout(function(){
//                var height = $('#popup_message').height();
//                console.log(height);
//            }, 5000);
            
//            $('#popup_message').mCustomScrollbar(SCROLL_OPTIONS);
//            $('#popup_message').height(height);
//            $('#popup_message').mCustomScrollbar("update");
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
    </script>
</head>
<body>
    <div class="headerbar">
        <div id="refresh">
            <div class="container_headerbar">
                <span style='cursor:pointer;color:rgb(70, 180, 220);font-weight: light;font-size:1.6em;' onclick='window.location.assign("home");'>Placeholder</span>
                <div style="position:absolute;right:500px;top:0;">
                    <div class="message" id="message_click">
                        <img id="message" class ="message" src='<?php echo System::INBOX_IMG_BLACK; ?>'></img>
                        <div id="messagediv" class="popup_div">
                            <div class="popup_top">
                                <span class='popup_header'>Messages</span>
                                <a href="message" class='user_preview_name' style='top:2px;font-size:12px;position:absolute;right:10px;'>- Compose</a>
                            </div>
                            <div id='popup_message' class="popup_content">
                                <ul class="message"></ul>
                            </div>
                        </div>
                        <span id='message_num' class="message_notification"></span>
                    </div>
                    <div class="notification" id="notification_click">
                        <img style='height:18px;' id="notification" class ="message" src='<?php echo System::NOTIFICATION_IMG_BLACK; ?>'></img><br>
                        <div id="notificationdiv" class="popup_div">
                            <div class="popup_top">
                                <span class='popup_header'>Notifications</span>
                            </div>
                            <div id='popup_notify' class="popup_content">
                                <ul class="notify"></ul>
                            </div>
                        </div>
                        <span id='notification_num' class="message_notification"></span>
                    </div>
                    <div class="network" id="network_click">
                        <img id="network" class="message network" src='<?php echo System::NETWORK_IMG_BLACK; ?>'></img><br>
                        <div id="networkdiv" class="popup_div">
                            <div class="popup_top">
                                <span class='popup_header'>Network</span>
                            </div>
                            <div id='popup_network' class="popup_content">
                                <ul class="network"></ul>
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
                
                <li style='background-image:url("Images/Icons/icons/application-plus.png");' onclick="show_group();" class="nav_option">
                    <a class="nav_option ellipsis_overflow">Create Group</a>
                </li>
            </ul>
        </div>
<!--        <button style='float:right;margin-top:10px;' class='pure-button-primary smallest' onclick='' title='Create a Group'>+</button>-->
        <?php
        if ($page_identifier != "inbox") {
            include_once ("friends_list.php");
        }
        ?>
    </div>
</body>
</html>