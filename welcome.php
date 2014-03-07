<?php
include_once('Scripts/lock.php');
include_once('Scripts/js.php');
?>
<!DOCTYPE HTML>
<head>
    <script>
        var event_files = new Array();
        var event_receivers = new Object();
        event_receivers.user = new Array();
        event_receivers.group = new Array();
        event_receivers.community = new Array();

        var group_receivers = new Object();
        group_receivers.user = new Array();
        group_receivers.group = new Array();
        group_receivers.community = new Array();

        var message_receivers = new Object();
        message_receivers.user = new Array();
        message_receivers.group = new Array();
        message_receivers.community = new Array();
        
        var sticky_receivers = new Object();
        sticky_receivers.user = new Array();
        sticky_receivers.group = new Array();
        sticky_receivers.community = new Array();
        var sticky_id = 0;

        function show_group() {
            dialog(
                    content = {
                        type: "html",
                        content: '<div class="pseudonym">\n\
             <table class="none" style="width:100%;">\n\
 <tr>\n\
 <td>\n\
<div class="timestamp"><span>Name</span></div>\n\
 <input style="border-radius:0; width:100%;" id="group_name_input" type="text" autocomplete="off" placeholder="Give the group a name..." />\n\
 </td>  </tr> <tr> <td>\n\
 <div class="timestamp"><span>About</span></div>\n\
<textarea style="border-radius:0; width:100%; height:100px;" id = "group_about" class="thin" placeholder="Write something about this group..."></textarea>\n\
</td> </tr> <tr> <td> <div class="timestamp"><span>Members</span></div>\n\
<div style="border-radius:0;"> \n\
</div> </td> </tr> </table> \n\
<table style="width:100%;"><tr><td class="group_names_slot"></td></tr></table> \n\
<input autocomplete="off" style="border-radius:0; width:100%;" type="text" id="names_input" class="search_input" placeholder="Add Member..." />\n\
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
                }, {
                    type: "neutral",
                    text: "Cancel",
                    onclick: function() {
                        removeDialog();
                    }
                }],
            properties = {
                modal: true,
                title: "Create a Group"
            });
        }


        $(document).on('click', '#names_universal .search_option', function(e)
        {
            var type = $(this).attr('entity_type');
            var info = '';
            if(type == 'user') {
                
            }
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
            $.post('Scripts/notifications.class.php', {action: "alert_num"}, function(response) {
                response = JSON.parse(response);
                if (response.message != '0') {
                    $('#message_num').text(response.message);
                    $('#message_num').show();
                    getMessageBox();
                }
                else {
                    $('#message_num').hide();
                }
                if (response.notification != '0') {
                    $('#notification_num').text(response.notification);
                    $('#notification_num').show();
                    getNotificationBox();
                }
                else {
                    $('#notification_num').hide();
                }
                if (response.network != '0') {
                    $('#network_num').text(response.network);
                    $('#network_num').show();
                    getNetworkBox();
                }
                else {
                    $('#network_num').hide();
                }

                var all = response.message + response.notification + response.network;
                var title = document.title.lastIndexOf(')');
                if (all > 0) {
                    if (title == -1) {
                        document.title = "(" + all + ") " + document.title;
                    }
                    else {
                        title = document.title.substring(title + 1);
                        document.title = "(" + all + ") " + title;
                    }
                }
                // $('#popup_message').mCustomScrollbar('scrollTo', 'top');
                // $('#popup_network').mCustomScrollbar('scrollTo', 'top');
                // $('#popup_notify').mCustomScrollbar('scrollTo', 'top');
                setTimeout(getNotificationNumber, 10000);
            });

        }
        function addreceiver(type, new_receiver, new_receiver_name, receivers, receivers_type)
        {
            var found;
            new_receiver = parseInt(new_receiver);
            if (type == 'user') {
                found = $.inArray(new_receiver, receivers.user);
            }
            if (type == 'group') {
                found = $.inArray(new_receiver, receivers.group);
            }
            if (type == 'community') {
                found = $.inArray(new_receiver, receivers.community);
            }
            if (found != -1) {
            }
            else
            {
                if (type == 'user') {
                    receivers.user.push(new_receiver);
                } else if (type == 'group') {
                    receivers.group.push(new_receiver);
                } else if (type == 'community') {
                    receivers.community.push(new_receiver);
                }
                if (receivers_type == "event") {
                    updateEventReceiverCount();
                }
                $('#names_input').val("");
                var html = "<div class='message_added_receiver message_added_receiver_" + new_receiver + "' search_type='"
                        + receivers_type + "' entity_type='"
                        + type + "' entity_id='" + new_receiver + "' ><div class='added_name'><span style='font-family:century gothic;'>" + new_receiver_name + "</span> \
                    <span class='delete_receiver'>x \
                    </span></div>";
                $('.' + receivers_type + '_names_slot').after(html);
                //.alert($('#' + receivers_type + '_names_slot').length);
            }
            alignDialog();
            return receivers;
        }

        function removereceiver(type, new_receiver, receivers)
        {
            var index;

            if (type == 'user') {
                index = receivers.user.indexOf(new_receiver);
                receivers.user.splice(index, 1);
            }
            if (type == 'group') {
                index = receivers.group.indexOf(new_receiver);
                receivers.group.splice(index, 1);
            }
            if (type == 'community') {
                index = receivers.community.indexOf(new_receiver);
                receivers.community.splice(index, 1);
            }
            //updateEventReceiverCount();
            return receivers;
        }

        function createGroup()
        {
            var group_name = $('#group_name_input').val();
            var group_about = $('#group_about').val();
            var group_type = $('#group_type').val();
            $.post("Scripts/group.class.php", {action: "create", group_name: group_name, group_about: group_about, group_type: group_type, invited_members: group_receivers}, function(response)
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
            var table = "<table style='min-height:100px;height:100px;width:100%;'><tr style='vertical-align:middle;'><td style='width:100%;text-align:center;'><div class='loader_outside_small'></div><div class='loader_inside_small'></div></td></tr></table>";
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
            $.post("Scripts/notifications.class.php", {action: "mark", type: type}, function(response) {
            });
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
        <div class="global_header">
            <div class="container_headerbar">
                <span style='cursor:pointer;color:white;font-weight: light;font-size:1.6em;' onclick='window.location.assign("home");'>Placeholder</span>
                <div style="position:absolute;right:500px;top:0;">
                    <div class="message" id="message_click">
                        <img id="message" class ="message" src='<?php echo System::INBOX_IMG; ?>'></img>
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
                        <img style='height:18px;' id="notification" class ="message" src='<?php echo System::NOTIFICATION_IMG; ?>'></img><br>
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
                        <img id="network" class="message network" src='<?php echo System::NETWORK_IMG; ?>'></img><br>
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
                               }' type='text' id='names_input' placeholder='Search for people, groups and files' name='receiver'>
                    <div class="search_results" id='names_universal'></div>
                </div>
                <div class="gear">
                    <a style = "cursor:pointer;">
                        <img id="gear_click" style="z-index:11; width:16px; height:16px; " class="logout_image_small message" src ="Images\Icons\Icon_Pacs\Batch-master\Batch-master\PNG\16x16\settings-2.png"></img>
                    </a>
                    <div style="display:none;" id="geardiv" class="geardiv">
                        <ul> 
                            <li class=""><a title"Logout" href="Scripts/logout.php">Logout</a></li> 
                            <li class=""><a href="">Privacy</a></li> 
                        </ul> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>