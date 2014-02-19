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

?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="CSS/message.css">
        <title>Inbox</title>
        <script>
            setInterval(refreshMessage, 5000);
            function refreshMessage() {
                $.post('Scripts/notification.class.php', {action:"updateMesssage", thread: <?php echo ($current_thread ? $current_thread: "null"); ?>}, function(response) {
                    response = $.parseJSON(response);
                    for(var i = 0; i <= response.length; i++) {
                        var table = $("");
                        table.append("<tr><td rowspan='2'><div style='background-size:cover;height:50px;width:50px;background-image: url(" + response[i]['src'] + ");'></div></td>");
                        table.append("<td><a href='user?id="+response[i]['e_id']+"'><span class='user_preview user_preview_name' user_id='"+response[i]['id']+"'></span></a></td>");
                        table.append("<td style='text-align:right;'><span class='message_convo_time'>" + response[i]['time'] + "</span></td></tr>");
                        table.append("<tr><td><span class='message_convo_text'>" + response[i]['message'] + "</span></td></tr>");
                        table.append("<tr><td colspan='3'><hr class='message_convo_seperator'></td></tr>");
                        $('table.message').append(table);
                    }
                    console.log(response);
                });
            }
            
            $(document).on('click', '#message_reply_button', function() {
                $(this).attr("disabled", "disabled");
                $(this).addClass("pure-button-disabled");
                sendMessage(true);
            });
            var message_bottom = true;
            var scroller;
            $(function() {
                var box_shadow = $('.message_convo_header').css('box-shadow');
                var MESSAGE_SCROLL = SCROLL_OPTIONS;
                MESSAGE_SCROLL.callbacks={onScroll:function(){message_bottom = false}, onTotalScroll:function(){message_bottom = true}};
                scroller = $('.message_convo_wrapper').mCustomScrollbar(MESSAGE_SCROLL);
                $('.message_convo_wrapper').mCustomScrollbar('scrollTo', 'bottom');
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
//                adjustSize('.message_reply_box', '.message_convo_wrapper', 45);
                
                autoresize('.message_reply_box');
            });
//            function alignMessage() {
//                
//                var width = $('.container').width();
//                $('.message_reply_container').width(width);
//                $('.message_reply_box').width(width - 12);
//                
//                $(document).on('propertychange keyup input change','.message_reply_box', function() {
//                    $('.message_convo_wrapper').css('bottom', $('.message_reply_box').height() + 50);
//                    scroller.mCustomScrollbar('update');
//                    if(message_bottom == true) {
//                        scroller.mCustomScrollbar('scrollTo', 'bottom');
//                    }
//                });
//            }

            $(document).keypress(function(e)
            {
                if (e.which == 13)
                {
                    $('#match').click();
                }
            });

            function sendMessage(reply)
            {
                var message, thread;
                if (reply == true)
                {
                    thread = <?php echo ($current_thread ? $current_thread: "null"); ?>;
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
                message = message.replace(/^\s+|\s+$/g,"");
                if (message == "")
                {
                    $('.message_reply_button').removeAttr('disabled');
                    $('.message_reply_button').removeClass('pure-button-disabled');
                   return;
                }
                $.post("Scripts/notifications.class.php", {action: "sendMessage", reply: reply, message: message, receivers: message_receivers, thread_id: thread}, function(response)
                {
                    if (response == "")
                    {
                        window.location.reload();
                        $('.message_reply_button').removeAttr('disabled');
                        $('.message_reply_button').removeClass('pure-button-disabled');
                    }
                    else
                    {
                        alert(response);
                    }
                });
            }
            <?php
            if(isset($_GET['r'])) {
                $receivers = unserialize($_GET['r']);
                echo "composeMessage();";
                foreach ($receivers as $receiver) {
                    echo "addreceiver(".$receiver.", '".$user->getName($receiver)."', message_receivers, 'message');";
                }
            }
            ?>

            $(function() {
                <?php
                if(isset($_GET['c'])) {
                    $receiver = base64_decode($_GET['c']);
                    echo "composeMessage();message_receivers = addreceiver('user', ".$receiver.", '".$user->getName($receiver)."', message_receivers, 'message');";
                }
                ?>
                $(".delete").hide();
                $("#deletebutton").click(function() {
                    $(".delete").toggle("slide");
                });
                $('.left_bar_container').append($('.messagecomplete'));

                $(document).on('input', '#names_input', function() {
                    search($(this).val(), 'message', '.message_names', function() {
                    });
                });
            });

            function composeMessage() {
                //title, content, button, properties
                var html = $('#compose_dialog').html();
                dialog(
                        {type: "html", content: html},
                [{text: "Send", type: "success", onclick: function() {
                            sendMessage(false);
                            dialogLoad();
                            $(this).attr('disabled', 'disabled');
                            $(this).addClass('pure-button-disabled');
                        }}],
                {modal: false, width: "50%", title: "Compose"}
                );
            }
        </script>
    </head>
    <body>
        <div hidden id='compose_dialog' style='display: none;'>
            <table><tr><td class='message_names_slot'></td><td>
                        <input autocomplete='off' id='names_input' class='search search_input message_search_input' placeholder='To...'>
                    </td></tr></table>
            <div style='max-height: 100px;' class='search_results message_search_results'></div>
            <textarea id='1message' placeholder='Message...' class='thin message_compose_box' style='height:200px;margin-top: 10px;'></textarea>
            <br/><br/>
        </div>
        <div class="global_container">
        <?php
        include_once 'left_bar.php';
        if (!isset($current_thread)) {
//                            
        }
        else {
            echo "<div class='container' style='height:80%;' id='compose'>";
            $messages = $notification->getMessage('thread', $current_thread);
            $messagecount = count($messages);
            if ($messagecount != 0) {
                echo "<div class='message_convo_header'>" . $notification->getReceivers($current_thread, 'header')
                . "</div><div class='message_convo_wrapper'><table class='message'>";

                foreach ($messages as $message) {
                    echo"<tr> <td rowspan='2' style='width:60px;'>"
                    . "<div style='background-size:cover;height:50px;width:50px;background-image:url("
                    . $user->getProfilePicture("chat", $message['user_id'])
                    . ");'></div></td>"
                    . "<td><a href='user?id="
                    . urlencode(base64_encode($message['user_id']))
                    . "'><span class='user_preview user_preview_name' user_id='"
                    . $message['user_id']
                    . "'>"
                    . $user->getName($message['user_id'])
                    . "<a/></td><td style='text-align:right;'><span class='message_convo_time'>"
                    . $system->date($message['time'])
                    . "</span></td></tr>"
                    . "<tr><td><span class='message_convo_text'>"
                    . $message['message']
                    . "</span></td></tr>"
                    . "<tr><td colspan='3'><hr class='message_convo_seperator'></td></tr>";
                }
                echo "</table></div><div class='message_reply_container'>"
                . "<textarea placeholder='Write a Reply...' class='message_reply_box' id='reply_value'></textarea>"
                . "<div class='textarea_clone'></div>"
                . "<div class='message_reply_options'>"
                . "<button style='float:right;' class='pure-button-primary small' id='message_reply_button'>Reply</button>"
                . "</div></div>";
            }
            else {
                echo "<div class='message_convo_wrapper scroll_thin' style='text-align:center;color:grey;font-weight:bolder;'>"
                . "You have no conversations. You can start one by clicking Compose.<br />"
                . "<span style='font-size:10em;cursor:pointer;' onclick='$(&quot;#message_compose&quot;).trigger(&quot;click&quot;);'>&#8617;</span>"
                . "</div>";
            }
            echo "</div>";
        }
        ?>	
        <?php include_once 'right_bar.php';?>
        </div>
    </body>
</html>