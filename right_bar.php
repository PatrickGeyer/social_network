<?php
require_once 'Scripts/calendar.class.php';
$calendar = new Calendar();
?>

<div class='right_bar_container'>
    <div class='calendar-container'>
    <!--  <div class="timestamp" style='margin-top: 0px;'>
         <span>Upcoming Events</span>
     </div> -->
    <?php
// if($user->getAttr('password') != "true") {
    //     echo "<div class='event event_expanded event_white'>"
    //     ."<div class='paste_pad' contenteditable></div></div>";
    // }
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false) {
        echo "<div class='event contentblock'>"
        . "<a href='https://www.google.co.uk/chrome'><button class='pure-button-blue pure-button-round'>Get Chrome</button></a></div>";
    }
    require 'chat.php';
    echo "</div>";
    ?>
</div>