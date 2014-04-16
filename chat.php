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
<?php foreach ($chat_rooms as $single_group) { ?>
            var i = "<?php echo $single_group['id']; ?>";
            Application.prototype.chat.room[i] = {};
            Application.prototype.chat.room[i] = {
                entry: new Array(),
                oldest: 0,
                newest: 998999999,
                getting_previous: false,
                last: false
            };
            $(function() {
                Application.prototype.chat.sendRequest('true', i);
            });
<?php } ?>
    </script>
</head>
<?php foreach ($chat_rooms as $single_group) { ?>
    <div class="chatcomplete contentblock" data-chat_room="<?php echo $single_group['id']; ?>">
        <div class='chatheader'>
            <div class="chat-head"></div>
            <div class='chat-info'>
                <div class='chat_feed_selector <?php echo ($chat_feed == $single_group['id'] ? "active_feed" : "") ?>'>
                    <p class='chat_header_text ellipsis_overflow'><?php echo $single_group['name']; ?></p>
                </div><br />
                <div class='chat-preview'></div>
            </div>
        </div>
        <div class="chat-container">
            <div></div>
            <div class="chatoutput" data-chat_room="<?php echo $single_group['id']; ?>" <?php echo($chat_feed != $single_group['id'] ? "style='display:none'" : ""); ?>>
                <div class='chat_loader' style='display:none;'><div class='loader_outside_small'></div><div class='loader_inside_small'></div></div>
                <ul style='max-width:225px;' class='chatreceive'>
                </ul>
            </div>
            <div class='text_input_container'>
                <textarea id="text" class="chatinputtext autoresize"  placeholder="Press Enter to send..."></textarea>
            </div>
        </div>
    </div>
<?php } ?>

<audio id='chat_new_message_sound'>
    <source src="Audio/newmessage.ogg" type="audio/ogg"></source>
    <source src="Audio/newmessage.mp3" type="audio/mpeg"></source>
    <source src="Audio/newmessage.wav" type="audio/wav"></source>
</audio>

<!--// $chat->getUnreadNum($single_group['id'], NULL)-->