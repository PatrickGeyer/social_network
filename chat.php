<?php
$chat_feed = 'y';
include_once('Scripts/chat.class.php');
$chat = new Chat();
if (!isset($_COOKIE['chat_feed'])) {
    //setcookie('chat_feed', 'y');
}
else {
    $chat_feed = $_COOKIE['chat_feed'];
}
?>
<head>
    <link rel="stylesheet" type="text/css" href="CSS/chat.css">
    <script id="chat_loader">

        var current_view = getCookie("chat_feed");
        if (current_view == "undefined") {
            current_view = "s";
        }

        var timer;
        var bottom = true;
        var oldest = 0;
        var newest = 99999999999999;
        var chat_ids = new Array();
        function iniScrollChat() {
            $('.chatoutput').on('scroll', function() {
                bottom = false;
                if ($(this).get(0).scrollTop + 400 > $(this).get(0).scrollHeight) {
                    bottom = true;
                } else if ($(this).get(0).scrollTop == 0) {
                    getPreviousChat();
                }
            });
            //detectChange();
            // var CHAT_SCROLL = SCROLL_OPTIONS;
//             CHAT_SCROLL.callbacks={onScroll: function() {bottom=false;}, onTotalScroll: function(){bottom=true;}, onTotalScrollBack: function() {getPreviousChat();}};
//             CHAT_SCROLL.scrollInertia = 0;
//             $('.chatoutput').mCustomScrollbar(CHAT_SCROLL);
        }
        function detectChange()
        {
            var key_height = $('.chatinputtext').outerHeight(true);
            var bottom = key_height;
            $('.chatoutput').css('bottom', bottom + "px");
            //$('.chatheader').css('bottom', bottom + "px");
            //var padding_top = $('.chatoutput').innerHeight() - $('#chatreceive').height() - bottom + 100;
            scroll2Bottom(false);
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
                if (current_view == 's')
                {
                    scrollH('#school_tab', '#feed_wrapper_scroller', 400);
                }
                else if (current_view == 'y')
                {
                    scrollH('#year_tab', '#feed_wrapper_scroller', 400);
                }
                else
                {
                    scrollH('#' + current_view, '#feed_wrapper_scroller', 400);
                }
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
                    setCookie('chat_feed', 'y', 5);
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
            sendChatRequest('true', current_view);
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
        var getting_previous = false;
        var last_chat = false;
        function getPreviousChat() {
            if (getting_previous == false) {
                getting_previous = true;

                var new_oldest = Math.max(chat_ids) - 20;
                if (new_oldest < 0) {
                    new_oldest = 0;
                }

                if (last_chat != true) {
                    var element = $('.chatoutput .single_chat:first');
                    $('.chat_loader').css('visibility', 'visible');
                    $.post("Scripts/chat.class.php", {chat: current_view, all: "previous", oldest: new_oldest, newest: oldest - 1}, function(response)
                    {
                        $('#chatreceive').prepend(styleChatResponse($.parseJSON(response)));
                        $('.chat_loader').css('visibility', 'hidden');
                        getting_previous = false;
                        element = element.offset().top;
                        $('.chatoutput').scrollTop(element);
                    });
                }
            }
        }
        function sendChatRequest(all, current)
        {
            $.post("Scripts/chat.class.php", {chat: current_view, all: all, oldest: oldest, newest: oldest}, function(response)
            {
                response = $.parseJSON(response);
                if (current == current_view)
                {
                    if (all == 'true')
                    {
                        $('.chatcomplete').fadeIn("fast");
                        $('#chatreceive').html(styleChatResponse(response));
                    }
                    else
                    {
                        $('#chatreceive').append(styleChatResponse(response));
                    }
                    if (all == 'false' && response.length > 0) {
                        $('#chat_new_message_sound').get(0).play();
                    }
                    setTimeout(function() {
                        sendChatRequest('false', current_view)
                    }, 1000);
                    detectChange();
                }
            });
        }
        function styleChatResponse(response) {
            var string = '';

            for (var i = response.length - 1; i >= 0; i--) {
                if (response[i]['type'] != 'event') {
                    string += "<li class='single_chat'><div class='chat_wrapper'><table cellspacing='0' cellpadding='0' style='width:100%;'><tr><td style='width:50px;padding-right:5px;'>";
                    string += "<div class='profile_picture_medium profile_picture_" + response[i]['user_id'] + "' style='border-left:2px solid lightgrey; float:left;";
                    string += "background-image:url(" + response[i]['pic'] + ");'></div></td><td>";
                    string += "<div class='chatname'><span class='user_preview user_preview_name chatname' style='margin-right:5px;font-size:13px;' user_id='" + response[i]['user_id'] + "'>" + response[i]['name'] + "</span></div>";
                    string += "<div class='chattext'>" + response[i]['text'] + "</div></td></tr><tr><td colspan='2' style='text-align:right;'>";
                    string += "<span class='chat_time post_comment_time'>" + response[i]['time'] + "</span></td></tr></table></div></li>";
                    if($.inArray(response[i]['id'], chat_ids) !== -1) {
                        console.log(response[i]['id']);
                        last_chat = true;
                        console.log(last_chat);
                        return "<div class='timestamp'><span>Start of Conversation</span></div>";
                    }
                    chat_ids.push(response[i]['id']);
                } else {
                    if (response[i]['code'] == 0) {
                        last_chat = true;
                    } else {
                        string += "<li class='single_chat'><div class='chat_wrapper'><table cellspacing='0' cellpadding='0' style='width:100%;'><tr><td style='width:50px;padding-right:5px;'>";
                        string += "<div class='chattext'>" + response[i]['text'] + "</div></td></tr><tr><td colspan='2' style='text-align:right;'>";
                    }
                }
            }
            ;

            newest = Math.max.apply(Math, chat_ids);

            var min = Math.min.apply(Math, chat_ids);
            // console.log("Min: " + min + " Oldest: " + oldest + " Newest: " + newest);
            oldest = min;
            scroll2Bottom(false);
            return string;
        }

        function scroll2Bottom(force)
        {
            //$(".chatoutput").mCustomScrollbar("update");
            if (bottom === true || force === true)
            {
                $('.chatoutput').get(0).scrollTop = $('.chatoutput').get(0).scrollHeight; //mCustomScrollbar("scrollTo", 'bottom');
            }
        }

        function submitchat(chat_text)
        {
            if (chat_text != "") {
                $('.chatinputtext').val('');
                $('.chatinputtext').attr('placeholder', "Sending...");
                $('.chatinputtext').attr('readonly', 'readonly');
                $.post("Scripts/chat.class.php", {action: "addchat", aimed: current_view, chat_text: chat_text}, function(response)
                {
                    $('.chatinputtext').removeAttr('readonly');
                    $('.chatinputtext').attr('placeholder', "Press Enter to send...");
                    scroll2Bottom(true);
                    bottom = true;
                });
            }
        }

        function change_chat_view(change_view)
        {
            $('#chatreceive').empty();
            clearTimeout(timer);
            current_view = change_view;
            sendChatRequest('true', current_view);
            setCookie('chat_feed', change_view, 5);
        }
    </script>
</head>
<div class="chatoff" id="chat_toggle"><?php
    if ($chat_feed == '0') {
        echo 'OFF';
    }
    else {
        echo 'ON';
    }
    ?></div>
<div class="chatcomplete" id="chat">
    <div id='feed_wrapper_scroller' class='scroll_thin_left chatheader'>
        <table>
            <tr>
                <td>
                    <div id='school_tab' feed_id='chat' style='padding:0px;' chat_feed='s' class='feed_selector chat_feed_selector 
                    <?php
                    if ($chat_feed == 's') {
                        echo "active_feed";
                    }
                    ?>'><h3 class='chat_header_text ellipsis-overflow'>
                         <?php
                         $chat_count = $chat->getUnreadNum(1, 'community', NULL);
                         echo "(" . $chat_count . ") ";
                         echo $user->getCommunityName();
                         ?>
                        </h3>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id='year_tab' style='padding:0px;' feed_id='chat' chat_feed='y' class='feed_selector chat_feed_selector 
                    <?php
                    if ($chat_feed == 'y') {
                        echo "active_feed";
                    }
                    ?>
                         '><h3 class='chat_header_text ellipsis-overflow'>Year <?php echo $user->getPosition(); ?></h3>
                    </div>
                </td>
            </tr>
            <?php
            $groups = $group->getUserGroups();
            foreach ($groups as $single_group) {
                echo "<tr><td><div style='padding:0px;' chat_feed='"
                . $single_group . "' feed_id='chat' class='feed_selector chat_feed_selector "
                . ($chat_feed == $single_group ? "active_feed" : "")
                . "' id='" . $single_group . "' title='"
                . $group->getGroupName($single_group) . "'><h3 class='chat_header_text ellipsis-overflow'>"
                . $chat->getUnreadNum($single_group, 'group', NULL)
                . $group->getGroupName($single_group) . "</h3></div></td></tr>";
            }
            ?>
        </table>
    </div>
    <div class="chatoutput">
        <div class='chat_loader' style='visibility:none;'><div class='loader_outside_small'></div><div class='loader_inside_small'></div></div>
        <ul style='max-width:225px;' class='chatbox' id="chatreceive">
            <script>//styleChatResponse($.parseJSON(<?php //$chat->getContent($chat_feed, 'true');  ?>));</script>
        </ul>
    </div>
    <div class='text_input_container'>
        <textarea id="text" class="thin chatinputtext autoresize"  placeholder="Press Enter to send..." style='font-size: 12px;border:0px;width:100%;resize:none;overflow:hidden;'></textarea>
        <div class='chat_input_clone textarea_clone' style='width: 100%; min-height: 30px;'></div>
        <?php //echo $chat_count; ?>
    </div>
</div>
<audio id='chat_new_message_sound'>
    <source src="Audio/newmessage.ogg" type="audio/ogg"></source>
    <source src="Audio/newmessage.mp3" type="audio/mpeg"></source>
    <source src="Audio/newmessage.wav" type="audio/wav"></source>
</audio>