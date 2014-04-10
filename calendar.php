<?php
function print_body() {
    if (TRUE) {
        ?>
        <div class="container">
            <script>
                var c = Application.prototype.calendar.print(<?php echo json_encode(Registry::get('calendar')->get_calendar(date("m"), date('Y'), array())); ?>);
                $('.container').append(c);
                document.title = "Calendar";
            </script>
        </div>
        <?php
    }
}
require_once('Scripts/lock.php');