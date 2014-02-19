<?php 
include_once 'Scripts/calendar.class.php';
$calendar = new Calendar();
?>

<div class='right_bar_container'>
    <?php 
    $quick_events = $calendar->getEvents(date('Y:m:d 00:00:00'), date('Y:m:d H:i:s', strtotime("+1 month")));
    $i = 0;
    foreach ($quick_events as $quick_event) {
        $i++;
        $classes = '';
        if($i < 5) {
            $classes = 'event_expanded';
        }
        echo "<div class='calendar-container'>" . $calendar->draw_event($quick_event, $classes) . "</div>"; 
    }?>
</div>