<?php
function print_body() {
if (isset($_GET['e'])) {
    $calendar = Registry::get('calendar');
    $event_id = $_GET['e'];
    if (isset($_GET['action'])) {
        $event_action = "edit";
    } else {
        $event_action = 'view';
    }
    $event = $calendar->getEvent($event_id);
    $event['time'] = strtotime($event['start']);
    echo "<title>Event: " . $event['title'] . "</title>";
    $assocFiles_num = count($event['files']);
    $receiver_num = count($event['receivers']['user'] + $event['receivers']['group']) - 1;
}
else {
    $event_action = "create";
    $event = array('title' => '', 'description' => '');
    echo "<title>Create Event</title>";
}

?>
<script>
    $(function() {
        var event_id = "<?php (isset($event['id']) ? $event['id'] : "''" ); ?>";
        var event_creator = "<?php (isset($event['user_id']) ? $event['user_id'] : "''") ?>";
        $('#datepicker').datetimepicker({
            <?php echo ($event_action == "edit" ? "value: '" .$event['start']. "'," : ""); ?>
            format: 'Y-m-d H:i:s',
            inline: true,
            lang: 'en',
            onChangeDateTime: function(dp, $input) {
                //alert($input.val())
            },
            scrollMonth: false
        });
        $('button#create_event').on('click', function() {
            var title = $('#event_title').val();
            var description = $('#event_description').val();
            var date = $('#datepicker').val();

            var data = {
                action: "<?php echo ($event_action=='edit' ? 'editEvent' : 'createEvent'); ?>",
                description: description,
                title: title,
                date: date,
                files: event_files,
                receivers: event_receivers,
                event_id: <?php echo ($event_action=='edit' ? $event_id : '""'); ?>
            }
            $.post('Scripts/calendar.class.php', data, function(response) {
                alert('done');
            });
        });
        function select_event_files() {

            <?php 
            if($event_action == 'edit') {
                foreach ($event['files'] as $file) {
                    echo "$(\"[file_id='" . $file['id'] . "']\").addClass('file_highlighted_green');";
                    echo "event_files.push(" . $file['id'] . ");";
                    //echo "console.log('".$file['id']."');";
                }
            }?>

            select_event_receivers();
        }
        function select_event_receivers() {
            <?php 
            if($event_action == 'edit') {
                foreach ($event['receivers']['user'] as $id) {
                    if ($id != $event['user_id'])
                        echo 'addreceiver("user", '.$id.', "' . Registry::get('user')->getName($id) . '", event_receivers, "event");';
                    }                
            }?>
        }
        $(document).on('click', '#delete_button', function() {
            var event_id = <?php echo ($event_action=='edit' || $event_action=='view' ? $event_id : '""'); ?>;
            $.post('Scripts/calendar.class.php', {action: "deleteEvent", event_id: event_id});
        });
        $(document).on('click', '#complete_button', function() {
            var event_id = <?php echo ($event_action=='edit' || $event_action=='view' ? $event_id : '""'); ?>;
            $.post('Scripts/calendar.class.php', {action: "completeEvent", event_id: event_id});
        });

        Application.prototype.FileList($('.file_box'), null, function() {
            select_event_files();
        });
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
</script>
    <div class='container'>
        <div style='margin-top:0px;' class='contentblock box_container'>
            <?php if ($event_action == "create" || $event_action == "edit"): ?>
                <h3><?php echo ($event_action == "create" ? "Create Event" : "Edit Event"); ?><button id='back_to_cal' class='pure-button-neutral'>Back to Calendar</button></h3>
                <ul>
                    <li class='section'>
                        <label class='settings'>Title</label><input id='event_title' type="text"/ value="<?php echo $event['title']; ?>">
                    </li>
                    <li class='section'>
                        <label class='settings'>Notes</label><textarea class='autoresize' id='event_description'><?php echo $event['description']; ?></textarea><div class='textarea_clone'></div>
                    </li>
                    <li class='section'>
                        <label class='settings'>Date & Time</label><input type="text" id="datepicker" />
                    </li>
                    <li class="section">
                         <label class='settings'>Attach Files</label>
                         <div class="file_box" style="max-height: 200px;"></div>
                    </li>
                    <li class='section'>
                        <label class='settings'>Share</label>
                        <div id='share_event_dialog'>
                            <div class='search_container'>
                                <div class='event_names_slot'></div>
                                <input class='search' mode='universal' />
                                <div id='share_event_results' class='search_results'>
                                    <div class='search_slider'></div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class='section'>
                        <button id='create_event' class='pure-button-green'><?php echo ($event_action == 'edit' ? "Save Event" : "Create Event"); ?></button>
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
                                                <?php echo Registry::get('user')->printTag($event['user_id']); ?>
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
                                                    echo "<script>$('.post_feed_media_wrapper').append(Application.prototype.file.print(".json_encode($files->format_file($file))."));</script>";
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
                                    echo "<div style='display:inline-block;'>" . Registry::get('user')->printTag($id) . "</div>";
                            }
                            ?>
                        </li>
    <?php endif; ?>
                    <li class='section'>
                        <?php if($event['user_id'] == Registry::get('user')->user_id) { ?>
                        <button id='delete_button' class='pure-button-warning'>Remove</button>
                        <?php } ?>
                        <a style='display:inline-block;' href='event?e=<?php echo $event_id; ?>&action=edit'><button class='pure-button-green'>Edit Event</button></a>
                        <button id='complete_button' class='pure-button-blue'>Complete</button>
                    </li>
                </ul>
<?php endif; ?>
        </div>
        </div>
    <?php 
    } 
	require 'Scripts/lock.php';
    ?>