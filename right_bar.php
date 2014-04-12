<?php
require_once 'Scripts/calendar.class.php';
$calendar = new Calendar();
?>

<div class='right_bar_container'>
    <div class='calendar-container'>
        <?php
        //     echo "";
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false) {
            echo "<div class='event contentblock'>"
            . "<a href='https://www.google.co.uk/chrome'><button class='pure-button-blue pure-button-round'>Get Chrome</button></a>"
            . "</div>";
        }
        ?>
    </div>
    <!--<div class='contentblock'><div class='paste_pad' contenteditable></div></div>-->
    <?php
    require 'chat.php';
    ?>
</div>
