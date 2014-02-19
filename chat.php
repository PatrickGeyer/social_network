<?php
$chat_feed = 'y';
include_once('Scripts/chat.class.php');
$chat = new Chat();
if (!isset($_COOKIE['chat_feed'])) {
    //setcookie('chat_feed', 'y');

} else {
    $chat_feed = $_COOKIE['chat_feed'];
}

?>
<head>
    <link rel="stylesheet" type="text/css" href="CSS/chat.css">
    <script id="chat_loader">

        var current_view = getCookie("chat_feed");
        if (current_view == "undefined") {
            current_view = "s";
            // setCookie("chat_feed", 's');
        }

        var timer;
        var bottom = true;
        function iniScrollChat() {
        var CHAT_SCROLL = SCROLL_OPTIONS;
        CHAT_SCROLL.callbacks={onScroll: bindChatScroll, onTotalScroll: function(){bottom=true;}};
        $('.chatoutput').mCustomScrollbar(CHAT_SCROLL);
        }
        $(window).resize(function(){
            $('.chatoutput').mCustomScrollbar("update");
            if(bottom == true) {
                $('.chatoutput').mCustomScrollbar("scrollTo", "bottom");
            }
        });
        $(function()
        {
            iniScrollChat();
            sendChatRequest('true', current_view);

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
                    $('#chat').hide('slide', {direction: 'right'}, 1000);
                }
                else
                {
                    setCookie('chat_feed', 'y', 5);
                    $('#chat_toggle').html("ON");
                    $('#chat').show('slide', {direction: 'right', duration: 0}, 1000);
                }
            });

            $(document).on('click', '.chat_feed_selector', function()
            {
                change_chat_view($(this).attr("chat_feed"));
            });
        });
        
        function detectChange()
        {
            var text = $('.chatinputtext').val();
            $('.chat_input_clone').text(text);
            $('.chatinputtext').height($('.chat_input_clone').height());
            var key_height = $('.chatinputtext').height() + 5;
            var bottom = 10 + key_height;
            $('.chatoutput').css('bottom', bottom + "px");
            $('.chatheader').css('bottom', bottom + "px");
            var padding_top = $('.chatoutput').innerHeight() - $('#chatreceive').height() - bottom + 100;
            scroll2Bottom();
        }
        $(function(){
            detectChange();
            $(document).on("propertychange keyup input change", '.chatinputtext', function(e){
                //Add type='application/pdf' to embed tags to prevent auto download
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
        </script>
        <script>
            

        function sendChatRequest(all, current)
        {
            $.post("Scripts/chat.class.php", {chat: current_view, all: all}, function(response)
            {
                if (current == current_view)
                {
                    if (all == 'true')
                    {
                        $('#chatreceive').html(response);
                        scroll2Bottom(true);                        
                    }
                    else
                    {
                        $('#chatreceive').append(response);
                        if(response != ""){
                            $('#chat_new_message_sound').get(0).play();
                        }
                    }
                    // time = setTimeout(function(){sendChatRequest('false', current_view), current_view}, 0);
                    setTimeout(function() {
                        sendChatRequest('false', current_view)
                    }, 1000);
                    scroll2Bottom();
                }
                $('#chat_loading_icon').hide();
            });
        }

        function bindChatScroll() {
            if($(".chatoutput").scrollTop() + $(".chatoutput").innerHeight() < $(".chatoutput")[0].scrollHeight){
                bottom = false;
            }    
        }

        function scroll2Bottom(force)
        {
            $(".chatoutput").mCustomScrollbar("update");
            if (bottom === true || force === true)
            {
                setTimeout(function(){$('.chatoutput').mCustomScrollbar("scrollTo", "bottom");  }, 100);
                
            }
        }

        function submitchat(chat_text)
        {
            if(chat_text != "") {
                $('.chatinputtext').val('');
                $('.chatinputtext').attr('placeholder', "Sending...");
                $('.chatinputtext').attr('readonly','readonly');
                $.post("Scripts/chat.class.php", {action: "addchat", aimed: current_view, chat_text: chat_text}, function(response)
                {
                    $('.chatinputtext').removeAttr('readonly');
                    $('.chatinputtext').attr('placeholder', "Press Enter to send...");
                    scroll2Bottom(true);
                });
            }
        }

        function change_chat_view(change_view)
        {
            $('#chatreceive').empty();
            $('#chat_loading_icon').show();
            clearTimeout(timer);
            current_view = change_view;

            sendChatRequest('true', current_view);

            if (change_view == 's')
            {
                //scrollH('#school_tab', '#feed_wrapper_scroller', 400);
            }
            else if (change_view == 'y')
            {
                //scrollH('#year_tab', '#feed_wrapper_scroller', 400);
            }
            else
            {
                //scrollH('#' + change_view, '#feed_wrapper_scroller', 400);
            }
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
                                 echo "(".$chat_count.") ";
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
            </tr>
        </table>
    </div>
    <div class="chatoutput">
        <img id='chat_loading_icon' src='Images/ajax-loader.gif'></img>
        <ul style='max-width:225px;' class='chatbox' id="chatreceive"></ul>
    </div>
    <div class='text_input_container'>
        <textarea id="text" class="thin chatinputtext"  placeholder="Press Enter to send..." style='font-size: 12px;border:0px;width:100%;resize:none;overflow:hidden;'></textarea>
        <div class='chat_input_clone' style='display:none;white-space: pre-wrap; width: 100%; min-height: 30px;  
    font-size: 12px;  
    padding: 0px;  
    word-wrap: break-word;  '></div>
    <?php //echo $chat_count; ?>
    </div>
</div>
<audio id='chat_new_message_sound'>
    <source src="Audio/newmessage.ogg" type="audio/ogg"></source>
    <source src="Audio/newmessage.mp3" type="audio/mpeg"></source>
    <source src="Audio/newmessage.wav" type="audio/wav"></source>
</audio>