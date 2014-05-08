<?php
if (!isset($page_identifier)) {
    $pid = "home";
}
print_header();
?>
<!DOCTYPE HTML>
<head>
    <script>
        var event_files = new Array();
        var event_receivers = new Object();
        event_receivers.user = new Array();
        event_receivers.group = new Array();
        var group_receivers = new Object();
        group_receivers.user = new Array();
        group_receivers.group = new Array();
        var message_receivers = new Object();
        message_receivers.user = new Array();
        message_receivers.group = new Array();
        var sticky_receivers = new Object();
        sticky_receivers.user = new Array();
        sticky_receivers.group = new Array();
        var sticky_id = 0;
        var audio_volume = 1;
        function show_group() {
            Application.prototype.UI.dialog(
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
<input autocomplete="off" style="border-radius:0; width:100%;" type="text" id="names_input" class="search" placeholder="Add Member..." />\n\
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
            if (found != -1) {
            }
            else
            {
                if (type == 'user') {
                    receivers.user.push(new_receiver);
                } else if (type == 'group') {
                    receivers.group.push(new_receiver);
                }
                $('#names_input').val("");
                var html = "<div class='message_added_receiver message_added_receiver_" + new_receiver + "' search_type='"
                        + receivers_type + "' entity_type='"
                        + type + "' entity_id='" + new_receiver + "' ><div class='added_name'><span style='font-family:century gothic;'>" + new_receiver_name + "</span> \
                    <span class='delete_receiver'>x \
                    </span></div>";
                $('.' + receivers_type + '_names_slot').after(html);
            }
            Application.prototype.UI.alignDialog();
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
            //updateEventReceiverCount();
            return receivers;
        }

        function createGroup() {
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
<?php

function print_header($PRINT = TRUE) {
    global $pid;
    if ($PRINT) {
        ?>
        <div class="headerbar">
            <div class="global_header">
            <?php if(isset($_COOKIE['id'])) { ?>
                <div class="circle">
                    <div class="ring">
                        <a href="home" class="menuItem fa fa-home fa-2x <?php
                        if ($pid == "home") {
                            echo "current_page";
                        }
                        ?>"></a>
                        <a href="message" class="menuItem fa fa-comment fa-2x <?php
                        if ($pid == "message") {
                            echo "current_page";
                        }
                        ?>"></a>
                        <a href="files" class="menuItem fa fa-play fa-2x <?php
                        if ($pid == "files") {
                            echo "current_page";
                        }
                        ?>"></a>
                        <a href="user" class="menuItem fa fa-user fa-2x <?php
                        if ($pid == "user") {
                            echo "current_page";
                        }
                        ?>"></a>
                        <a href="calendar" class="menuItem fa fa-calendar fa-2x <?php
                        if ($pid == "calendar") {
                            echo "current_page";
                        }
                        ?>"></a>
                        <a href="Scripts/logout.php" class="menuItem fa fa-sign-out fa-2x no-ajax"></a>
                    </div>
                    <a href="#" class="no-ajaxy center"></a>
                </div>
                <div class='global_header_container'>
                    <div class='search_container contentblock' style='float:left;'>
                        <input class="search_box search" autocomplete='off' mode='universal' type='text' id='names_input' placeholder='Search for people, groups and files' name='receiver'>
                        <div class="search_results"></div>
                    </div>
                    <div class='global_header_icon_container'>
                        <div class="message" id="message_click">
                            <img id="message" class ="message" src='<?php $base = Registry::get('base');
                   echo $base::INBOX_IMG; ?>'></img>
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
                            <img style='height:18px;' id="notification" class ="message" src='<?php echo $base::NOTIFICATION_IMG; ?>'></img><br>
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
                            <img id="network" class="message network" src='<?php echo $base::NETWORK_IMG; ?>'></img><br>
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
                <div class='contentblock global_media_container'></div>
            
            <?php } else { ?>
            <div class='global_header_container'>
            	<div class="loginbox">
                	<input type="text" spellcheck="false" placeholder="Email" autocomplete="off" tabindex="1" class='email_login'/>
                	<input type="password" spellcheck="false" tabindex="2" placeholder="Password" autocomplete="off" class='password_login'/>
                	<button onclick='logIn();' class='pure-button-secondary small'>Login</button>
<!--                 	<a href='signup?m'><button class='pure-button-neutral small signup_button'>Signup</button><a/> -->
            	</div>
            <div class='logo'>
            	<div></div>
            	<h3>PLACEHOLDER</h3>
            </div>
            <?php } ?>
        </div>
    </div> 
</div>

    
        
        <!-- 
<div class='files_space_container'>
            <div class='files_space_meter' style='height:<?php echo Registry::get('files')->getUsedSize(); ?>%;'></div>
        </div>
 -->
        <?php
    }
}
?>