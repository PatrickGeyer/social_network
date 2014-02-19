<?php
if (!isset($page_identifier)) {
    $page_identifier = "none_set";
}
?>
<div class='left_bar_container'>
    <div class="navigation">
        <a href='user?id=<?php echo urlencode(base64_encode($user->getId())); ?>'>
            <div class="user_info 
            <?php
            if ($page_identifier == "user") {
                echo "current_page_user";
            }
            ?>
                 " style='cursor:pointer; margin-bottom:10px;'>
                <table cellspacing='0' cellpadding='0'>
                    <tr style='vertical-align:top;'>
                        <td>
                            <div class='welcome_user_profile_picture' style='background-image:url("<?php echo $user->getProfilePicture("chat"); ?>");'></div>
                        </td>
                        <td>
                            <div class='welcome_user_info'>
                                <span class="current_user_name_edit">
                                    <?php
                                    echo $system->trimStr($user->getName(), 20);
                                    ?>
                                </span>
                                <br />
                                <span class='edit_user_text'>
                                    <?php
                                    echo $system->trimStr($user->getCommunityName(), 15);
                                    ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </a>
        <hr style='z-index:-1;width:200px;border:0px;border-bottom: 0px solid lightgrey;'>
        <ul class="navigation_list"> 
            <li style='background-image:url("Images/Icons/icons/home.png");' class="nav_option ellipsis_overflow 
            <?php
            if ($page_identifier == "home") {
                echo "current_page";
            }
            ?>
                "><a class="nav_option ellipsis_overflow" href="home">Home</a></li> 
            <li style='background-image:url("Images/Icons/icons/paper-plane.png");' class="nav_option 
            <?php
            if ($page_identifier == "school") {
                echo "current_page";
            }
            ?>
                "><a class="nav_option ellipsis_overflow" href="community?id=<?php echo urlencode(base64_encode($user->getCommunityId())); ?>"><?php echo $user->getCommunityName(); ?></a></li> 
            <li style='background-image:url("Images/Icons/icons/paper-clip.png");' class="nav_option <?php
            if ($page_identifier == "files") {
                echo "current_page";
            }
            ?>"><a class="nav_option ellipsis_overflow" href="files">My Files</a></li> 
            <li style='background-image:url("Images/Icons/icons/mail.png");' class="nav_option <?php
            if ($page_identifier == "inbox") {
                echo "current_page";
            }
            ?>"><a class="nav_option ellipsis_overflow" href="message">Inbox</a></li>

            <li style='background-image:url("Images/Icons/icons/application-plus.png");' onclick="show_group();" class="nav_option">
                <a class="nav_option ellipsis_overflow">Create Group</a>
            </li>
        </ul>
    </div>
    <!--        <button style='float:right;margin-top:10px;' class='pure-button-primary smallest' onclick='' title='Create a Group'>+</button>-->
    <?php
    if ($page_identifier != "inbox") :
        include_once ("friends_list.php");
    else : ?>
            <div class="messagecomplete">
            <div id="message" class="messagehi">
                <div id= "messagetoolbox" class="messagetoolbox" style="border-left: 1px solid lightgrey;">
                    <button onclick='composeMessage();' title="Compose a message" class="pure-button-primary smallest" id='message_compose'>Compose</button>
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
                                echo "<li thread_id='".$threadnumber."' id='inbox_message_"
                                . $threadnumber. "' class='"
                                . ($resultmes['read'] == 0 ? 'message_inbox_item' : 'message_inbox_item_read')
                                . ($current_thread == $threadnumber ? ' message_active_thread' : " message_inactive_thread") . " message_inbox_item'>"
                                . "<div class='message_wrapper'>"
                                . "<div class='delete_cross delete_cross_top delete_message' style='background-image:url(\"".Base::DELETE."\")'></div>"
                                . "<table style='width:200px; table-layout:fixed' cellspacing='0' cellpadding='0'><tr><td style='width:50px;'>"
                                . "<div class='message_user_profile'>"
                                . $notification->getMessagePicture("chat", $resultmes['thread']) . "</div></td><td>"
                                . "<div class='message_info_preview'><p class='ellipsis_overflow message_user_name "
                                . ($current_thread == $resultmes['thread'] ? ' message_active_thread' : "message_inactive_thread") . "'>"
                                . $notification->getReceivers($resultmes['thread'], 'list')
                                . "</p><p class='ellipsis_overflow message_text_preview "
                                . ($current_thread == $resultmes['thread'] ? ' message_active_thread' : "message_inactive_thread") . "'>" . $resultmes['message'] . "</p></div>"
                                . "</td></tr></table></div></li>";
                            }
                            array_push($threadcount, $threadnumber);
                        }

                        if ($messagecount == 0) {
                            echo "<li class='inbox_message' style='text-align: center;'>No messages</li> ";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
   <?php endif; ?>
</div>