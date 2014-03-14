<?php
$chat_feed = 'undefined';
include_once('Scripts/chat.class.php');
$chat = new Chat();
$chat_rooms = $chat->get_chat_rooms();
if (!isset($_COOKIE['chat_feed'])) {
    $chat_feed = $chat_rooms[0]['id'];
}
else {
    $chat_feed = $_COOKIE['chat_feed'];
}
$chat_rooms = $chat->get_chat_rooms();
?>
<head>
    <link rel="stylesheet" type="text/css" href="CSS/chat.css">
    <script id="chat_loader">
        var chat_room = <?php echo $chat_feed ?>;
        var loaded_chat_room = <?php echo $chat_feed ?>;
        setCookie("chat_feed", chat_room);


        var timer;
        var bottom = true;
        var getting_previous = new Array();
        var last_chat = new Array();
        var oldest = new Array();
        var newest = new Array();
        var chat_ids = new Array();
        <?php foreach($chat_rooms as $single_group) { ?>
            chat_ids[<?php echo $single_group['id']; ?>] = new Array();
            oldest[<?php echo $single_group['id']; ?>] = 0;
            newest[<?php echo $single_group['id']; ?>] = 99999999;
            getting_previous[<?php echo $single_group['id']; ?>] = false;
            last_chat[<?php echo $single_group['id']; ?>] = false;
            $(function() {
                sendChatRequest('true', <?php echo $single_group['id']; ?>);
            });
        <?php } ?>
        function iniScrollChat() {
            $('.chatoutput').on('scroll', function() {
                bottom = false;
                //console.log($(this).get(0).scrollTop + " - " + ($(this).get(0).scrollHeight - $(this).get(0).offsetHeight));
                if ($(this).get(0).scrollTop + 20 >= $(this).get(0).scrollHeight - $(this).get(0).offsetHeight) {
                    bottom = true;
                } else if ($(this).get(0).scrollTop == 0) {
                    getPreviousChat($(this).data('chat_room'));
                }
            });
        }
        function detectChange()
        {
            var key_height = $('.chatinputtext').outerHeight(true);
            var bottom = key_height;
            $('.chatoutput').css('bottom', bottom + "px");
            //$('.chatheader').css('bottom', bottom + "px");
            //var padding_top = $('.chatoutput').innerHeight() - $('.chatreceive').height() - bottom + 100;
            scroll2Bottom(false, chat_room); //USE EVEN IF NOT LOADED
        }
        $(window).resize(function() {
            detectChange();
        });
        $(function()
        {
            iniScrollChat();
            var cookie = getCookie("chat_feed");
            if (cookie == 0)
            {
                $('#chat').hide();
            }
            else
            {
                    //scrollH('#' + current_view, '#feed_wrapper_scroller', 400);
            }
            $("#chat_toggle").click(function()
            {
                var cookie = getCookie("chat_feed");
                if (cookie > 0 || isNaN(cookie))
                {
                    setCookie('chat_feed', 0, 5);
                    $('#chat_toggle').html("OFF");
                    $('#chat').hide('slide', {direction: 'right'}, 500);
                }
                else
                {
                    $('#chat_toggle').html("ON");
                    $('#chat').show('slide', {direction: 'right', duration: 0}, 500);
                }
            });

            $(document).on('click', '.chat_feed_selector', function()
            {
                change_chat_view($(this).attr("chat_feed"));
            });
        });

        $(function() {
            $(document).on("propertychange keyup input change", '.chatinputtext', function(e) {
                if (e.keyCode == 13)
                {
                    if (e.shiftKey !== true)
                    {
                        submitchat($(this).val());
                        e.preventDefault();
                    }
                }
                detectChange();
            });
        })
        function getPreviousChat(chat_index) {
            if (getting_previous[chat_index] == false) {
                getting_previous[chat_index] = true;

                var new_oldest = Array.min(chat_ids[chat_index]) - 20;
                if (new_oldest < 0) {
                    new_oldest = 0;
                }

                if (last_chat[chat_index] != true) {
                    var element = $('[data-chat_room="' + chat_index + '"] .single_chat:first');
                    $('[data-chat_room="' + chat_index + '"] .chat_loader').slideDown('fast');
                    $.post("Scripts/chat.class.php", {chat: chat_index, all: "previous", oldest: new_oldest, newest: oldest[chat_index] - 1}, function(response)
                    {
                        response = $.parseJSON(response);
                        if (response.length == 0) {
                            //console.log(new_oldest + " ; " + oldest);
                            last_chat[chat_index] = true;
                            $('[data-chat_room="' + chat_index + '"] .chatreceive').prepend("<div class='timestamp'><span>Start of Conversation</span></div>");
                            $('[data-chat_room="' + chat_index + '"] .chat_loader').slideUp('fast');
                            return;
                        }
                        $('[data-chat_room="' + chat_index + '"] .chatreceive').prepend(styleChatResponse(response, chat_index));
                        $('[data-chat_room="' + chat_index + '"] .chat_loader').slideUp('fast');
                        getting_previous[chat_index] = false;
                        element = element.offset().top;
                        $('[data-chat_room="' + chat_index + '"]').scrollTop(element);
                    });
                }
            }
        }
        function sendChatRequest(all, chat_index)
        {
            $.post("Scripts/chat.class.php", {chat: chat_index, all: all, oldest: 0, newest: newest[chat_index]}, function(response)
            {
                $('[data-chat_room="' + chat_index + '"] .chat_loader').slideUp('fast');
                response = $.parseJSON(response);

                    if (all == 'true')
                    {
                        $('.chatcomplete').fadeIn("fast");
                        $('[data-chat_room="' + chat_index + '"] .chatreceive').append(styleChatResponse(response, chat_index));
                    }
                    else
                    {
                        $('[data-chat_room="' + chat_index + '"] .chatreceive').append(styleChatResponse(response, chat_index));
                    }
                    if (all == 'false' && response.length > 0) {
                        $('#chat_new_message_sound').get(0).play();
                        if(chat_index != chat_room) {
                            $('.chat_feed_selector[chat_feed="' + chat_index + '"] *').css('color', 'red');
                            //alert('unread in another chat');
                        }
                    }
                    setTimeout(function() {
                        sendChatRequest('false', chat_index);
                    }, 500);
                    detectChange();
            });
        }
        function styleChatResponse(response, chat_index) {
            var string = '';
            if (response.length == 0) {

            }
            for (var i = response.length - 1; i >= 0; i--) {
                if (response[i]['type'] != 'event') {
                    string += "<li class='single_chat'><div class='chat_wrapper'><table cellspacing='0' cellpadding='0' style='width:100%;'><tr><td style='width:50px;padding-right:5px;'>";
                    string += "<div class='profile_picture_medium profile_picture_" + response[i]['user_id'] + "' style='border-left:2px solid lightgrey; float:left;";
                    string += "background-image:url(\"" + response[i]['pic'] + "\");'></div></td><td>";
                    string += "<div class='chatname'><span class='user_preview user_preview_name chatname' style='margin-right:5px;font-size:13px;' user_id='" + response[i]['user_id'] + "'>" + response[i]['name'] + "</span></div>";
                    string += "<div class='chattext'>" + response[i]['text'] + "</div></td></tr><tr><td colspan='2' style='text-align:right;'>";
                    string += "<span class='chat_time post_comment_time'>" + response[i]['time'] + "</span></td></tr></table></div></li>";
                    if ($.inArray(response[i]['id'], chat_ids[chat_index]) !== -1) {
                        //console.log(response[i]['id']);
                        //last_chat = true;
                        // console.log(last_chat);
                        //return "<div class='timestamp'><span>Start of Conversation</span></div>";
                    }
                    chat_ids[chat_index].push(response[i]['id']);
                    //console.log(chat_ids[chat_index]);
                } else {
                    if (response[i]['code'] == 0) {
                        //last_chat = true;
                    } else {
                        string += "<li class='single_chat'><div class='chat_wrapper'><table cellspacing='0' cellpadding='0' style='width:100%;'><tr><td style='width:50px;padding-right:5px;'>";
                        string += "<div class='chattext'>" + response[i]['text'] + "</div></td></tr><tr><td colspan='2' style='text-align:right;'>";
                    }
                }
            }
            ;

            newest[chat_index] = Array.max(chat_ids[chat_index]);
            oldest[chat_index] = Array.min(chat_ids[chat_index]);
            scroll2Bottom(false, chat_index);
            return string;
        }

        function scroll2Bottom(force, chat_index)
        {
            if (bottom === true || force === true)
            {
                $('[data-chat_room="' + chat_index + '"]').get(0).scrollTop = $('[data-chat_room="' + chat_index + '"]').get(0).scrollHeight + 2000;
            }
        }

        function submitchat(chat_text)
        {
            if (chat_text != "") {
                $('.chatinputtext').val('');
                $('.chatinputtext').attr('placeholder', "Sending...");
                $('.chatinputtext').attr('readonly', 'readonly');
                $.post("Scripts/chat.class.php", {action: "addchat", aimed: chat_room, chat_text: chat_text}, function(response)
                {
                    $('.chatinputtext').removeAttr('readonly');
                    $('.chatinputtext').attr('placeholder', "Press Enter to send...");
                    scroll2Bottom(true, chat_room);
                    bottom = true;
                });
            }
        }

        function change_chat_view(change_view)
        {
            $('.chat_feed_selector[chat_feed="' + change_view + '"] *').css('color', 'black');
//            $('.chat_loader').slideDown('fast');
            $('.chatoutput').hide();
            $('[data-chat_room="' + change_view + '"]').show();
//            clearTimeout(timer);
            chat_room = change_view;
            //current_view = change_view;
            scroll2Bottom(true, chat_room);
            setCookie('chat_feed', change_view, 5);
        }
    </script>
</head>
<div class="chatcomplete" id="chat">
    <div id='feed_wrapper_scroller' class='chatheader'>
        <table><tr>
                <?php
                foreach ($chat_rooms as $single_group) {
                    echo "<td><div style='padding:0px;' chat_feed='"
                    . $single_group['id'] . "' feed_id='chat' class='feed_selector chat_feed_selector "
                    . ($chat_feed == $single_group['id'] ? "active_feed" : "")
                    . "'  title='" . $single_group['name'] . "'><h3 class='chat_header_text ellipsis_overflow'>"
                    //. $chat->getUnreadNum($single_group['id'], NULL)
                    . $single_group['name'] . "</h3></div></td>";
                }
                ?>
            </tr></table>
    </div>
    <?php foreach ($chat_rooms as $single_group) { ?>
        <div class="chatoutput" data-chat_room="<?php echo $single_group['id']; ?>" <?php echo($chat_feed != $single_group['id'] ? "style='display:none'" : ""); ?>>
            <div class='chat_loader' style='display:none;'><div class='loader_outside_small'></div><div class='loader_inside_small'></div></div>
            <ul style='max-width:225px;' class='chatreceive'>
                <li><?php echo $single_group['name']; ?></li>
                <script>//styleChatResponse($.parseJSON(<?php //$chat->getContent($chat_feed, 'true');   ?>));</script>
            </ul>
        </div>
    <?php } ?>
    <div class='text_input_container'>
        <textarea id="text" class="thin chatinputtext autoresize"  placeholder="Press Enter to send..." style='font-size: 12px;border:0px;width:100%;resize:none;overflow:hidden;'></textarea>
        <div class='chat_input_clone textarea_clone' style='width: 100%; min-height: 30px;'></div>
        <?php //echo $chat_count; ?>
    </div>
    <div class="chatoff" id="chat_toggle"><?php
        if ($chat_feed == '0') {
            echo 'OFF';
        }
        else {
            echo 'ON';
        }
        ?></div>
</div>
<audio id='chat_new_message_sound'>
    <source src="Audio/newmessage.ogg" type="audio/ogg"></source>
    <source src="Audio/newmessage.mp3" type="audio/mpeg"></source>
    <source src="Audio/newmessage.wav" type="audio/wav"></source>
</audio>