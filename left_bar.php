<?php
if (!isset($page_identifier)) {
    $page_identifier = "none_set";
}
?>
<div class='left_bar_container'>
    <?php
    if ($page_identifier != "inbox") :
    else : ?>
            <div class="messagecomplete">
            <div id="message" class="messagehi">
<!--                <div id= "messagetoolbox" class="messagetoolbox" style="border-left: 1px solid lightgrey;">
                    <button onclick='composeMessage();' title="Compose a message" class="pure-button-blue smallest" id='message_compose'>Compose</button>
                </div>-->
                <div style='border:0;max-height:65%;overflow-x:hidden;' class="scroll_thin">
                    <ul class="message_list_container">
                        <?php
//                        $threadcount = array();
//                        $allMessages = $notification->getMessage();
//                        $messagecount = 0;
//                        foreach ($allMessages as $resultmes) {
//                            $messagecount++;
//                            $threadnumber = $resultmes['thread'];
//                            if (!in_array($threadnumber, $threadcount)) {
//                                echo "<li thread_id='".$threadnumber."' id='inbox_message_"
//                                . $threadnumber. "' class='"
//                                . ($resultmes['read'] == 0 ? 'message_inbox_item' : 'message_inbox_item_read')
//                                . ($current_thread == $threadnumber ? ' message_active_thread' : " message_inactive_thread") . " message_inbox_item'>"
//                                . "<div class='message_wrapper'>"
//                                . "<div class='delete_cross delete_cross_top delete_message' style='background-image:url(\"".Base::DELETE."\")'></div>"
//                                . "<table style='width:200px; table-layout:fixed' cellspacing='0' cellpadding='0'><tr><td style='width:50px;'>"
//                                . "<div class='message_user_profile'>"
//                                . $notification->getMessagePicture("chat", $resultmes['thread']) . "</div></td><td>"
//                                . "<div class='message_info_preview'><p class='ellipsis_overflow message_user_name "
//                                . ($current_thread == $resultmes['thread'] ? ' message_active_thread' : "message_inactive_thread") . "'>"
//                                . $notification->getReceivers($resultmes['thread'], 'list')
//                                . "</p><p class='ellipsis_overflow message_text_preview "
//                                . ($current_thread == $resultmes['thread'] ? ' message_active_thread' : "message_inactive_thread") . "'>" . $resultmes['message'] . "</p></div>"
//                                . "</td></tr></table></div></li>";
//                            }
//                            array_push($threadcount, $threadnumber);
//                        }
//
//                        if ($messagecount == 0) {
//                            echo "<li class='inbox_message' style='text-align: center;'>No messages</li> ";
//                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
   <?php endif; ?>
</div>