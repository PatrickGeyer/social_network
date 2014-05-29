<?php
$chat_feed = 'undefined';
include_once('Scripts/chat.class.php');
$chat = Registry::get('chat');
$chat_rooms = $chat->get_chat_rooms();
if (!isset($_COOKIE['chat_feed'])) {
	$chat_feed = "";
	if(isset($chat_rooms[0]['id'])) {
    	$chat_feed = $chat_rooms[0]['id'];
    }
}
else {
    $chat_feed = $_COOKIE['chat_feed'];
}
$chat_rooms = $chat->get_chat_rooms();
?>
<head>
    <script id="chat_loader">
        var chat_room = '<?php echo $chat_feed ?>';
        var loaded_chat_room = <?php echo $chat_feed ?>;
        setCookie("chat_feed", chat_room);

        var timer;
<?php foreach ($chat_rooms as $single_group) { ?>
            var Chat = new Application.prototype.Chat(<?php echo $single_group['id']; ?>, '<?php echo $single_group['name']; ?>');
<?php } ?>
    </script>
</head>

<audio id='chat_new_message_sound'>
    <source src="/Audio/newmessage.ogg" type="audio/ogg"></source>
    <source src="/Audio/newmessage.mp3" type="audio/mpeg"></source>
    <source src="/Audio/newmessage.wav" type="audio/wav"></source>
</audio>

<!--// $chat->getUnreadNum($single_group['id'], NULL)-->