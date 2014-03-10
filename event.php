<?php
if (isset($_GET['e'])) {
    include_once('Scripts/calendar.class.php');
    $calendar = Calendar::getInstance();
    $event = $_GET['e'];
    $event = $calendar->getEvent($event);
    $event['time'] = strtotime($event['start']);
    echo "<title>Event: " . $event['title'] . "</title>";
    $assocFiles_num = count($event['files']);
    $receiver_num = count($event['receivers']['user'] + $event['receivers']['group'] + $event['receivers']['community']) - 1;
}
else {
    $event = "create";
    echo "<title>Create Event</title>";
}

include_once('welcome.php');
include_once('chat.php');
?>
<script src='<?php echo Base::DATETIMEPICKER; ?>'></script>
<link rel="stylesheet" href="<?php echo Base::DATETIMEPICKER_CSS; ?>" />
<script>
    $(function() {
        var event_id = "<?php (isset($event['id']) ? $event['id'] : "''" ); ?>";
        var event_creator = "<?php (isset($event['user_id']) ? $event['user_id'] : "''") ?>";
        $('#datepicker').datetimepicker({
            format: 'Y-m-d H:i:s',
            inline: true,
            lang: 'en',
            onChangeDateTime: function(dp, $input) {
                //alert($input.val())
            }
        });
        $('button#create_event').on('click', function() {
            var title = $('#event_title').val();
            var description = $('#event_description').val();
            var date = $('#datepicker').val();

            $.post('Scripts/calendar.class.php', {action: "createEvent", title: title, description: description, date: date, files: event_files, receivers: event_receivers}, function(response) {
                history.go(-1);
            });
        });
        $('button#share_event').on('click', function() {
            var content = $('#share_event_dialog').html();
            dialog({
                type: "html",
                content: content
            },
            [{
                    text: "OK",
                    type: "success",
                    onclick: function() {
                        removeDialog();
                    }
                }],
            {
                title: "Share this Event"
            });
        });
        $(document).on('click', '#delete_button', function() {
            $.post('Scripts/calendar.class.php', {action: "deleteEvent", event_id: event_id, event_creator: event_creator});
        });

        fileList($('.file_box'), null);
        $('.file_box').on('click', 'div.file_item', function() {
            var file = $(this).data('file');
            if ($(this).hasClass('file_highlighted_green')) {
                event_files = removeFromArray(event_files, file.id);
            } else {
                event_files.push(file.id);
            }
            $(this).toggleClass('file_highlighted_green');
        });

        $('#back_to_cal').on('click', function() {
            history.go(-1);
        });
    });

    function updateEventReceiverCount() {
        var count = event_receivers.user.length + event_receivers.group.length + event_receivers.community.length;
        $('#event_share_receiver_count').html(count + " other receivers");
    }
</script>
<div class="global_container">
    <?php include_once 'left_bar.php'; ?>
    <div style='padding-top: 20px;' class='container'>

        <div hidden style='display:none' id='share_event_dialog'>
            <table>
                <tr>
                    <td class='event_names_slot'></td>
                </tr>
            </table>
            <input class='search' mode='universal' />
            <div id='share_event_results' class='search_results'></div>
        </div>

        <div style='margin-top:0px;' class='box_container'>
            <?php if ($event == "create"): ?>
                <h3>Create Event<button id='back_to_cal' class='pure-button-neutral'>Back to Calendar</button></h3>
                <ul>
                    <li class='section'>
                        <label class='settings'>Title</label><input id='event_title' type="text"/>
                    </li>
                    <li class='section'>
                        <label class='settings'>Notes</label><textarea class='autoresize' id='event_description'></textarea><div class='textarea_clone'></div>
                    </li>
                    <li class='section'>
                        <table cellspacing="0">
                            <tr>
                                <td>
                                    <label class='settings'>Date & Time</label><input type="text" id="datepicker" />
                                </td>
                                <td>
                                    <label class='settings'>Attach Files</label>
                                    <div class="file_box" style="width: 300px;max-height: 200px;"></div>
                                </td>
                            </tr>
                        </table>
                    </li>
                    <li class='section'>
                        <button id='create_event' class='pure-button-success'>Create Event</button>
                        <button id='share_event' class='pure-button-primary'>Share Event</button>
                        <span id='event_share_receiver_count' class='post_comment_time'></span>
                    </li>
                </ul>
            <?php else: ?>
                <h3><?php echo $event['title']; ?><button id='back_to_cal' class='pure-button-neutral'>Back to Calendar</button></h3>
                <ul>
                    <li class='section'>
                        <table style='width:100%;'>
                            <tr>
                                <td style='padding-right:10px;'>
                                    <table>
                                        <tr>
                                            <td>
                                                <label class='settings'><?php echo $event['title']; ?></label>
                                                <p class='settings' style='padding-left:0px;padding-top:0px;'><?php echo $event['description']; ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <table style='float:right;'>
                                        <tr>
                                            <td colspan='2'>
                                                <?php echo $calendar->widget($event['time']); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo $user->printTag($event['user_id']); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <ul>
                                        <?php if ($assocFiles_num > 0) : ?>
                                            <li class='section'>
                                                <label class='settings'>Attached Files (<?php echo $assocFiles_num; ?>)</label>
                                                <?php
                                                echo "<div class='post_feed_media_wrapper' activity_id='" . $event['id'] . "'>";
                                                foreach ($event['files'] as $file) {
                                                    echo "<script>$('.post_feed_media_wrapper').append(print_file(".json_encode($files->format_file($file))."));</script>";
                                                }
                                                echo "</div>";
                                                ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </li>
                    <?php if ($receiver_num != 0) : ?>
                        <li class="section">
                            <label class="settings">Shared With (<?php echo $receiver_num; ?>)</label>
                            <?php
                            foreach ($event['receivers']['user'] as $id) {
                                if ($id != $event['user_id'])
                                    echo "<div style='display:inline-block;'>" . $user->printTag($id) . "</div>";
                            }
                            ?>
                        </li>
    <?php endif; ?>
                    <li class='section'>
                        <button id='delete_button' class='pure-button-secondary'>Delete</button>
                        <button class='pure-button-success'>Edit Event</button>
                        <button class='pure-button-primary'>Complete</button>
                    </li>
                </ul>
<?php endif; ?>
        </div>
    </div>
    <?php include_once 'right_bar.php';?>
</div>