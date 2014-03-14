<?php 
include_once 'Scripts/calendar.class.php';
$calendar = new Calendar();
?>

<div class='right_bar_container'>
    <div class="timestamp" style='margin-top: 0px;'>
        <span>Upcoming Events</span>
    </div>
    <?php 
    $quick_events = $calendar->getEvents(date('Y:m:d 00:00:00'), date('Y:m:d H:i:s', strtotime("+1 month")));
    $i = 0;
    echo "<div class='calendar-container'>";
    foreach ($quick_events as $quick_event) {
        $i++;
        $classes = '';
        if($i < 7) {
        	if($i < 4) {
            	$classes = 'event_expanded';
            }
            echo $calendar->draw_event($quick_event, $classes); 
        }
    }
    // if($user->getAttr('password') != "true") {
    //     echo "<div class='event event_expanded event_white'>"
    //     ."<div class='paste_pad' contenteditable></div></div>";
    // }
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false) {
        echo "<div class='event event_expanded event_white'>"
        ."<a href='https://www.google.co.uk/chrome'><button class='pure-button-primary pure-button-round'>Get Chrome</button></a></div>";
    }
    echo "</div>";
?>
</div>