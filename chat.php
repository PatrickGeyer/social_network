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