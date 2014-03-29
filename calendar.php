<?php
require_once('Scripts/lock.php');

function print_body() {
    global $calendar;
    if (TRUE) {
        ?>
        <div class="container">
            <script>
                var c = print_calendar(<?php echo json_encode($calendar->get_calendar(date("m"), date('Y'), array())); ?>);
                $('.container').append(c);
            </script>
        </div>
        <?php
    }
}
