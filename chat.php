<?php
if (!isset($_COOKIE['showchat'])) {
    setcookie('showchat', 'y');
}
?>
<head>
    <link rel="stylesheet" type="text/css" href="CSS/chat.css">
    <script id="chat_loader">

        var current_view = getCookie("showchat");

        var timer;
        var bottom = true;

        $(function()
        {
            scroll2Bottom();
            sendChatRequest('true', current_view);

            var cookie = getCookie("showchat");
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
                var cookie = getCookie("showchat");
                if (cookie > 0 || isNaN(cookie))
                {
                    setCookie('showchat', 0, 5);
                    $('#chat_toggle').html("OFF");
                    $('#chat').hide('slide', {direction: 'right'}, 1000);
                }
                else
                {
                    setCookie('showchat', 'y', 5);
                    $('#chat_toggle').html("ON");
                    $('#chat').show('slide', {direction: 'right', duration: 0}, 1000);
                }
            });

            $(document).on('click', '.feed_selector', function()
            {
                $('.chat_selector').removeClass('active_feed');
                $(this).addClass('active_feed');

                change_chat_view($(this).attr("chat_feed"));
            });
        });

        function sendChatRequest(all, current)
        {
            $.post("Scripts/chat.class.php", {chat: current_view, all: all}, function(response)
            {
                if (current == current_view)
                {
                    if (all == 'true')
                    {
                        $('#chatreceive').html(response);
                    }
                    else
                    {
                        $('#chatreceive').append(response);
                    }
                    // time = setTimeout(function(){sendChatRequest('false', current_view), current_view}, 0);
                    sendChatRequest('false', current_view);
                    scroll2Bottom();
                }
                $('#chat_loading_icon').hide();
                $('img').error(function() {
                    $(this).attr('src', 'Images/profile-picture-default-unknown-chat.jpg');
                    $(this).addClass('chat_broken_image');
                });
            });
        }

        $(function()
        {
            $('.chatoutput').bind('scroll', function()
            {
                if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight)
                {
                    bottom = true;
                }
                else
                {
                    bottom = false;
                }
            })
        }
        );

        function scroll2Bottom(force)
        {
            if (bottom == true || force == true)
            {
                $(".scroll_thin").mCustomScrollbar("update");
                $(".chatoutput").mCustomScrollbar("scrollTo", 'bottom');
            }
        }

        function submitchat(chat_text)
        {
            $.post("Scripts/chat.class.php", {action: "addchat", aimed: current_view, chat_text: chat_text}, function(response)
            {
                $("#text").css("color", "black");
                $('#text').html('');
                scroll2Bottom(true);
            });
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
            setCookie('showchat', change_view, 5);
        }
    </script>
</head>
<div class="chatoff" id="chat_toggle"><?php
    if ($_COOKIE['showchat'] == '0') {
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
                                <div id='school_tab' style='padding:0px;' chat_feed='s' class='feed_selector chat_selector 
                                <?php
                                if ($_COOKIE['showchat'] == 's') {
                                    echo "active_feed";
                                }
                                ?>
                                '><h3 class='chat_header_text'>
                                         <?php
                                         echo $system->trimStr($user->getCommunityName(), 10);
                                         ?>
                                </h3></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div id='year_tab' style='padding:0px;' chat_feed='y' class='feed_selector chat_selector 
                                <?php
                                if ($_COOKIE['showchat'] == 'y') {
                                    echo "active_feed";
                                }
                                ?>
                                '><h3 class='chat_header_text'>Year <?php echo $user->getPosition();?></h3>
                                </div>
                            </td>
                        </tr>
                            <?php
                            $groups = $group->getUserGroups();
                            foreach ($groups as $single_group) {
                                echo "<tr><td><div style='padding:0px;' chat_feed='"
                                . $single_group . "' class='feed_selector chat_selector "
                                . ($_COOKIE['showchat'] == $single_group ? "active_feed" : "")
                                . "' id='" . $single_group . "' title='"
                                . $group->getGroupName($single_group) . "'><h3 class='chat_header_text'>"
                                . $system->trimStr($group->getGroupName($single_group), 6) . "</h3></div></td></tr>";
                            }
                            ?>
                        </tr>
                    </table>
                </div>
                <div class="chatoutput scroll_thin">
                    <img id='chat_loading_icon' src='Images/ajax-loader.gif'></img>
                    <ul style='max-width:225px;' class='chatbox' id="chatreceive"></ul>
                </div>
                <div class='text_input_container'>
                    <div contenteditable id="text" onkeydown='
                            if (event.keyCode == 13)
                            {
                                if (event.shiftKey !== true)
                                {
                                    submitchat($(this).html());
                                    $(this).html("Sending...");
                                    $(this).css("color", "lightgrey");
                                    return false;
                                }
                            }
                            detectChange();
                            return true;'
                         class="chatinputtext"  data-placeholder="Press Enter to send...">
                    </div>
                </div>
</div>
</div>
<script>
    detectChange();
    function detectChange()
    {
        var key_height = $('.chatinputtext').height() + 5;
        var bottom = 25 + key_height;
        $('.chatoutput').css('bottom', bottom + "px");
        $('.chatheader').css('bottom', bottom + "px");
        var padding_top = $('.chatoutput').innerHeight() - $('#chatreceive').height() - bottom + 100;

        if (padding_top > 0)
        {
            //console.log(padding_top);
            // $('.chatoutput').css('padding-top', padding_top + "px");
        }
        scroll2Bottom();
        setTimeout(detectChange, 100);
    }
</script>