<?php

function print_body() {
    
}

require_once('/Scripts/lock.php');
$page_identifier = "inbox";
$current_thread;
?>
<script>
    document.title = "Inbox";
    $(document).on('click', '#message_reply_button', function() {
        $(this).attr("disabled", "disabled");
        $(this).addClass("pure-button-disabled");
        sendMessage(true);
    });
    var message_bottom = true;
    var message_newest = null;
    var message_oldest = null;
    var scroller;
    $(function() {
<?php
if (isset($_GET['r'])) {
    $receivers = unserialize($_GET['r']);
    echo "composeMessage();";
    foreach ($receivers as $receiver) {
        echo "message_receivers = addreceiver(" . $receiver . ", '" . $user->getName($receiver) . "', message_receivers, 'message');});";
    }
}
else if (isset($_GET['c'])) {
    $receivers = array();
    $current_thread = NULL;
    $receiver = $_GET['c'];
    $uid = json_encode($notification->format_receivers($receivers));
    echo "composeMessage();message_receivers = addreceiver('user', " . $receiver . ", '" . $user->getName($receiver) . "', message_receivers, 'message');";
}
else if (isset($_GET['thread'])) {
    $current_thread = $_GET['thread'];
    $notification->markMessageRead("thread", $current_thread);
//Registry::get('db')->query("UPDATE message_share SET `read` = 1, seen = 1 WHERE receiver_id =" . $user->getId() . " AND thread_id = " . $current_thread . ";");
}
else {
    $current_thread = $notification->getRecentThread();
}

if (isset($_GET['id'])) {
    $messageid = $_GET['id'];
    $notification->markMessageRead("id", $messageid);
    if (!Registry::get('db')->query("UPDATE message_share SET `read` = 1, seen=1 WHERE id = " . $messageid . ";")) {
        
    }
}
?>
        var box_shadow = $('.message_convo_header').css('box-shadow');
        var MESSAGE_SCROLL = SCROLL_OPTIONS;
        MESSAGE_SCROLL.callbacks = {onScroll: function() {
                message_bottom = false
            }, onTotalScroll: function() {
                message_bottom = true
            }};
        scroller = $('.message_convo_wrapper').mCustomScrollbar(MESSAGE_SCROLL);
        $('.message_convo_wrapper').mCustomScrollbar('scrollTo', 'bottom');
        //alignMessage();
        $(window).resize(function() {
            //alignMessage();
        });
        $('.message_convo_wrapper').scroll(function() {
            alert('fe');
            if ($(this).scrollTop() < 10) {
                $('.message_convo_header').css('box-shadow', 'none');
            }
            else {
                $('.message_convo_header').css('box-shadow', box_shadow);
            }

            var last_chat = $('.message_convo_wrapper .message_convo_message:first');
            get_message(message_oldest - 20, message_oldest - 1, function(string) {
                $('.message_convo_container').prepend(string);
            });
        });
        autoresize('.message_reply_box');
    });
    get_message(message_oldest, message_newest, function(string) {
        $('.message_convo_container').append(string);
    });
    function get_message(min, max, callback) {
        $.post('/Scripts/notifications.class.php', {action: "get_thread", min: min, max: max, thread: <?php echo ($current_thread ? $current_thread : "null"); ?>}, function(response) {
            var string = '';
            response = $.parseJSON(response);
            for (var i in response) {
                string += format_message(response[i]);
                if (response[i].id > message_newest) {
                    message_newest = response[i].id;
                }
            }
            callback(string);
        });
    }
    function format_message(message) {
        var table = "";
        table += ("<div class='message_convo_message'>");
        table += "<div class='timestamp'><span>" + message.time + "</span></div><div class='profile_picture_medium' style='background-image: url(\"" + message.user.pic + "\");'></div>";
        table += ("<a href='user?id=" + message.user.id + "'><span class='user_preview user_preview_name' user_id='" + message.id + "'>" + message.user.name + "</span></a>");
        table += ("<div class='message_convo_message_text'><span class='message_convo_text'>" + message.message + "</span></div>");
        table += ("</div>");
        return table;
    }

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
            thread = <?php echo ($current_thread ? $current_thread : "null"); ?>;
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
        message = message.replace(/^\s+|\s+$/g, "");
        if (message == "")
        {
            $('.message_reply_button').removeAttr('disabled');
            $('.message_reply_button').removeClass('pure-button-disabled');
            return;
        }
        $.post("/Scripts/notifications.class.php", {action: "sendMessage", reply: reply, message: message, receivers: message_receivers, thread_id: thread}, function(response)
        {
            if (response == "")
            {
                //window.location.reload();
                get_message(parseInt(message_newest) + 1, message_newest + 200, function(string) {
                    $('.message_convo_container').append(string);
                    scroll_to_bottom(scroller, message_bottom, true);
                    //$('.message_convo_wrapper').mCustomScrollbar('scrollTo', 'bottom');
                });
                $('#message_reply_button').removeAttr('disabled');
                $('#message_reply_button').removeClass('pure-button-disabled');
            }
            else
            {
                alert(response);
            }
        });
    }


    $(function() {
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
        Application.prototype.UI.dialog(
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
<div hidden id='compose_dialog' style='display: none;'>
    <table><tr><td class='message_names_slot'></td><td>
                <input autocomplete='off' id='names_input' class='search search_input message_search_input' placeholder='To...'>
            </td></tr></table>
    <div style='max-height: 100px;' class='search_results message_search_results'></div>
    <textarea id='1message' placeholder='Message...' class='thin message_compose_box' style='height:200px;margin-top: 10px;'></textarea>
    <br/><br/>
</div>
<?php
if (!isset($current_thread)) {
//                            
}
else {
    $messages = $notification->getMessage('thread', $current_thread);
    $messagecount = count($messages);
    if ($messagecount != 0) {
        echo "<div class='message_convo_header'>" . $notification->getReceivers($current_thread, 'header')
        . "</div><div class='message_convo_wrapper'><div class='message_convo_container'>";

        //MESSAGES

        echo "</div></div><div class='message_reply_container'>"
        . "<textarea placeholder='Write a Reply...' class='message_reply_box' id='reply_value'></textarea>"
        . "<div class='textarea_clone'></div>"
        . "<div class='message_reply_options'>"
        . "<button style='float:right;' class='pure-button-blue small' id='message_reply_button'>Reply</button>"
        . "</div></div>";
    }
    else {
        echo "<div class='message_convo_wrapper scroll_thin' style='text-align:center;color:grey;font-weight:bolder;'>"
        . "You have no conversations. You can start one by clicking Compose.<br />"
        . "<span style='font-size:10em;cursor:pointer;' onclick='$(&quot;#message_compose&quot;).trigger(&quot;click&quot;);'>&#8617;</span>"
        . "</div>";
    }
}
?>	
</div>