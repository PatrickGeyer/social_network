<?php
include_once('Scripts/lock.php');
$page_identifier = "inbox";
$current_thread;
include_once('welcome.php');
include_once('chat.php');

if (isset($_GET['thread'])) {
    $current_thread = $_GET['thread'];
    $notification->markMessageRead("thread", $current_thread);
    //$database_connection->query("UPDATE message_share SET `read` = 1, seen = 1 WHERE receiver_id =" . $user->getId() . " AND thread_id = " . $current_thread . ";");
}
else {
    $current_thread = $notification->getRecentThread();
}

if (isset($_GET['id'])) {
    $messageid = $_GET['id'];
    $notification->markMessageRead("id", $messageid);
    if (!$database_connection->query("UPDATE message_share SET `read` = 1, seen=1 WHERE id = " . $messageid . ";")) {
        
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    if (!mysql_query("DELETE FROM messages WHERE thread = " . $_POST['message_id'] . ";")) {
        echo "error/" . mysql_error();
    }
    else {
        die("success/" . $_POST['message_id']);
    }
}
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="CSS/message.css">
        <title>Inbox</title>
        <script>
            $(function() {
                var box_shadow = $('.message_convo_header').css('box-shadow');
                scrollToBottom('.message_convo_wrapper', true);
                alignMessage();
                $(window).resize(function() {
                    alignMessage();
                });
                $('.message_convo_wrapper').scroll(function() {
                    if ($(this).scrollTop() < 10) {
                        $('.message_convo_header').css('box-shadow', 'none');
                    }
                    else {
                        $('.message_convo_header').css('box-shadow', box_shadow);
                    }
                });
                adjustSize('.message_reply_box', '.message_convo_wrapper', 45);
            });
            function alignMessage() {
                var width = $('.container').width();
                $('.message_reply_container').width(width);
                $('.message_reply_box').width(width - 12);
            }

            function getnamesmessage(value)
            {
                $.post("Scripts/searchbar.php", {search: "message", input_text: value}, function(data) {
                    $('.message_names').html(data);
                });
            }

            $(document).keypress(function(e)
            {
                if (e.which == 13)
                {
                    $('#match').click();
                }
            });

            var receivers = new Array();
            function addreceivermessage(new_receiver, new_receiver_name)
            {
                var found = $.inArray(new_receiver, receivers);
                if (found != -1){}
                else
                {
                    console.log("Added to receiver list: " + new_receiver_name);
                    receivers.push(new_receiver);
                    $('#names_input').val('');
                    var html = "<div class='message_added_receiver message_added_receiver_" + 
                            new_receiver + "'><span style='font-family:century gothic;'>" + 
                            new_receiver_name + "</span>" +
                        "<span class='message_delete_receiver' onclick='removereceivermessage(" + 
                        new_receiver + ");'>x" +
                        "</span></div>";
                    $('.message_search_input').before(html);
                }
                alignDialog();
            }
            function removereceivermessage(receiver_id)
            {
                var index = receivers.indexOf(receiver_id);
                if (index > -1)
                {
                    receivers.splice(index, 1);
                }
                $('.message_added_receiver_' + receiver_id).remove();
                alignDialog();
            }
            function sendMessage(reply)
            {
                var message, thread;
                if (reply == true)
                {
                    thread = <?php echo $current_thread; ?>;
                }
                else
                {

                }
                if (reply == true) {
                    message = $('.message_reply_box').val();
                }
                else {
                    message = $('.message_compose_box').last().val();
                }
                $.post("Scripts/notifications.class.php", {action: "sendMessage", reply: reply, message: message, receivers: receivers, thread_id: thread}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        window.location.replace("message?thread=" + status[1]);
                    }
                    else
                    {
                        alert(response);
                    }
                });
            }
        </script>
        <script>
            $(function() {
                $(".delete").hide();
                $("#deletebutton").click(function() {
                    $(".delete").toggle("slide");
                });
            });
            function deleteMessage(message_id)
            {
                $.post("message.php", {action: "delete", message_id: message_id}, function(response)
                {
                    var status = response.split("/");
                    if (status[0] == "success")
                    {
                        $('#' + message_id).slideUp();
                    }
                });
            }
            function composeMessage() {
                //title, content, button, properties
                var html = $('#compose_dialog').html();
                dialog(
                        {type: "html", content: html},
                [{text: "Send", type: "success", onclick: function() {
                        sendMessage(false);
                    }}],
                {modal: false, width: "50%", title: "Compose"}
                );
            }
        </script>
    </head>
    <body>
        <div hidden id='compose_dialog' style='display: none;'>
            <input autocomplete='off' onkeyup='search(this.value, &quot;message&quot;, &quot;.message_names&quot;, function(){});' id='names_input' class='message_search_input' placeholder='To...'>
            <div style='max-height: 100px;border:1px solid lightblue;margin-bottom: 5px; display:none;' class='search_results message_names'></div>
            <textarea id='1message' placeholder='Message...' class='thin message_compose_box' style='height:200px;'></textarea><br/><br/>
        </div>
        <div class="messagecomplete">
            <div id="message" class="messagehi">
                <div id= "messagetoolbox" class="messagetoolbox">
                    <button onclick='composeMessage();' title="Compose a message" class="pure-button-primary smallest">Compose</button>
                    <!--<button id='deletebutton' class="pure-button-error smallest">Delete</button>-->
                </div>
                <div style='border:0;max-height:65%;overflow-x:hidden;' class="scroll_thin">
                    <ul class="message_list_container">
                        <?php
                        $threadcount = array();
                        $allMessages = $notification->getMessage();
                        $messagecount = 0;
                        foreach ($allMessages as $resultmes) {
                            $messagecount++;
                            $threadnumber = $resultmes['thread'];
                            if (!in_array($threadnumber, $threadcount)) {
                                $user_profile = "SELECT id FROM users WHERE id=" . $resultmes['sender_id'] . ";";
                                $user_profile = $database_connection->prepare($user_profile);
                                $user_profile->execute();
                                $user_profile = $user_profile->fetchAll();
                                echo "<li onclick='window.location.assign(&quot;message?thread=" . $resultmes['thread'] . "&quot;);' id='"
                                . $resultmes['thread'] . "' class='"
                                . ($resultmes['read'] == 0 ? 'message_inbox_item' : 'message_inbox_item_read')
                                . ($current_thread == $resultmes['thread'] ? ' message_active_thread' : "") . "'>"
                                . "<div class='message_wrapper'>"
                                . "<table cellspacing='0' cellpadding='0'><tr><td>"
                                . "<div class='message_user_profile' style='background-image:url(\""
                                . $user->getProfilePicture("chat", $resultmes['sender_id']) . "\");'></div></td><td>"
                                . "<div class='message_info_preview'><span class='message_user_name'>"
                                . $user->getName($resultmes['sender_id'])
                                . "</span><br><span class='message_text_preview'>" . $system->trimStr($resultmes['message'], 40) . "</span></div>"
                                . "<button class='pure-button-error small delete' hidden onclick='deleteMessage(" . $threadnumber
                                . ");'>Delete</button></td></tr></table></div></li>";
                            }
                            array_push($threadcount, $threadnumber);
                        }

                        if ($messagecount == 0) {
                            echo "<li style='padding:20px; text-align: center;'>No messages</li> ";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        if (!isset($current_thread)) {
//                            
        }
        else {
            echo "<div class='container' style='bottom:20px;' id='compose'>"
            . "<div class='message_convo_header'>" . $notification->getReceivers($current_thread, true)
            . "</div><div class='message_convo_wrapper scroll_thin'><table class='message'>";

            foreach ($notification->getMessage('thread', $current_thread) as $message) {
                echo"<tr> <td rowspan='2' style='width:60px;'>"
                . "<div style='background-size:cover;height:50px;width:50px;background-image:url("
                . $user->getProfilePicture("chat", $message['sender_id']) . ");'></div></td>"
                . "<td><a href='user?id=" . urlencode(base64_encode($message['sender_id']))
                . "'><span class='user_preview user_preview_name' user_id='" . $message['sender_id'] . "'>"
                . $user->getName($message['sender_id'])
                . "<a/></td><td style='text-align:right;'><span class='message_convo_time'>"
                . $system->humantiming($message['time']) . "</span></td></tr>"
                . "<tr><td><span class='message_convo_text'>" . $message['message'] . "</span></td></tr>"
                . "<tr><td colspan='3'><hr style='width:100%; border-bottom:1px solid lightgrey;'></td></tr>";
            }
            echo"</table></div><div class='message_reply_container'>"
            . "<textarea placeholder='Write a Reply...' class='message_reply_box' id='reply_value'></textarea>"
            . "<div class='message_reply_options'>"
            . "<button style='float:right;' class='pure-button-primary small' onclick='sendMessage(true);'>Reply</button>"
            . "</div></div></div>";
        }
        ?>	
    </body>
</html>