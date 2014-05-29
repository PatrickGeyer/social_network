<?php

function print_body() { ?>
    <script>
    document.title = "Inbox";
    var message_bottom = true;
    var message_newest = null;
    var message_oldest = null;
    var scroller;
    $(function() {
        var box_shadow = $('.message_convo_header').css('box-shadow');
//        scroller = $('.message_convo_wrapper').mCustomScrollbar(MESSAGE_SCROLL);
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
    });

    $(document).keypress(function(e) {
        if (e.which == 13)
        {
            $('#match').click();
        }
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
<?php }
$page_identifier = "inbox";
require_once('/Scripts/lock.php');
$current_thread;
?>