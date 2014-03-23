<?php

include_once('database.class.php');
include_once('system.class.php');
include_once('user.class.php');
include_once('files.class.php');

class Calendar {

    private static $calendar = NULL;
    protected $system;
    protected $user;
    protected $database_connection;
    private $files;
    public $event_types = array();

    public function __construct() {
        $this->user = User::getInstance();
        $this->system = System::getInstance();
        $this->database_connection = Database::getConnection();
        $this->files = Files::getInstance();
        $this->event_types['Homework'] = array('color' => 'rgb(50, 150, 50)');
    	$this->event_types['Event']['color'] = 'rgb(0, 40, 180)';
    	$this->event_types['Meeting']['color'] = 'orange';
    }

    public static function getInstance() {
        if (self :: $calendar) {
            return self :: $calendar;
        }

        self :: $calendar = new Calendar();
        return self :: $calendar;
    }

    // function draw_calendar($month, $year, $events = array()) {
    //     $events = $this->getEvents(date('Y-m-d H:i:s', strtotime('-1 month')), date('Y-m-d H:i:s', strtotime('+1 month')));
    //     $calendar = '<div class="box_container"><h3>'.date('F Y', strtotime($year."-".$month)).'<button class="pure-button-neutral" id="create_event">Create Event</button></h3><table cellpadding="0" cellspacing="0" class="calendar">';

    //     /* table headings */
    //     $headings = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    //     $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

    //     /* days and weeks vars now ... */
    //     $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
    //     $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
    //     $days_in_this_week = 1;
    //     $day_counter = 0;
    //     $dates_array = array();

    //     /* row for week one */
    //     $calendar.= '<tr class="calendar-row calendar-events-row">';

    //     /* print "blank" days until the first of the current week */
    //     for ($x = 0; $x < $running_day; $x++):
    //         $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
    //         $days_in_this_week++;
    //     endfor;

    //     /* keep going with days.... */
    //     for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
    //         $event_day = $year . '-' . $month . '-' . $list_day;
    //         $date = new DateTime($event_day);
    //         $event_day = $date->format('Y-m-d');
    //         $calendar.= '<td class="calendar-day'.($event_day == date_format(new DateTime(), 'Y-m-d') ? "-current" : ""). ' "><div>';
    //         /* add in the day number */
    //         $calendar.= '<div class="day-number">' . $list_day . '</div>';
    //         foreach ($events as $event) {   
    //             if(date_format(new DateTime($event['start']), 'Y-m-d') == $event_day) {
    //                 $calendar .= $this->draw_event($event);
    //             }
    //         }
    //         $calendar.= '</div></td>';
    //         if ($running_day == 6):
    //             $calendar.= '</tr>';
    //             if (($day_counter + 1) != $days_in_month):
    //                 $calendar.= '<tr class="calendar-row  calendar-events-row">';
    //             endif;
    //             $running_day = -1;
    //             $days_in_this_week = 0;
    //         endif;
    //         $days_in_this_week++;
    //         $running_day++;
    //         $day_counter++;
    //     endfor;

    //     /* finish the rest of the days in the week */
    //     if ($days_in_this_week < 8):
    //         for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
    //             $calendar.= '<td class="calendar-day-np">&nbsp;';
    //             $calendar .= "</td>";
                
    //         endfor;
    //     endif;

    //     $calendar.= '</tr>';
    //     $calendar.= '</table></div>';
    //     return $calendar;
    // }
    
    function get_calendar($month, $year, $events = array()) {
        $calendar = array();
        
        $calendar['date'] = date('F Y', strtotime($year."-".$month));
        $calendar['year'] = $year;
        $calendar['month'] = $month;
        $calendar['running_day'] = date('w', mktime(0, 0, 0, $month, 1, $year));
        $calendar['days_in_month'] = date('t', mktime(0, 0, 0, $month, 1, $year));
        $calendar['days_in_this_week'] = $days_in_this_week = 1;
        
        $day_counter = 0;
        $dates_array = array();
        
        $calendar['event'] = $this->get_events(date('Y-m-d H:i:s', strtotime('-1 month')), date('Y-m-d H:i:s', strtotime('+1 month')));
        return $calendar;
    }
    
    function format_event($event) {
        $event['event_day'] = date_format(new DateTime($event['start']), 'Y-m-d');
        $event['color'] = $this->event_types[$event['type']]['color'];
        $sql = "SELECT complete FROM event_status WHERE event_id=:event_id "
                . "AND user_id=:user_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":event_id" => $event['id'],
            ":user_id" => $this->user->user_id
        ));
        $event['complete'] = $sql->fetchColumn();
        if($event['complete'] == 1) {
            $event['color'] = "rgb(0,140,280);opacity:0.2;";
        }
        $event['file'] = $this->getAssocFiles($event['id']);
        return $event;
    }

    function draw_event($event, $classes = '') {
        $calendar = '';
        $event['files'] = $this->getAssocFiles($event['id']);
        $file_string = '<table>';
        foreach ($event['files'] as $file) {
            $file_string .= '<tr><td>';
            $file_string .= "<div class='profile_picture_icon' style='vertical-align:top;display:inline-block;background-image: url(\""
                    . $this->files->getFileTypeImage($file, 'ICON') . "\");'></div>";
            $file_string .= "</td><td><a href='" . $file['path']
                    . "' download><p style='margin:0px;padding:0px;max-width:120px;' class='ellipsis_overflow'>"
                    . $file['name'] . "</p></a><br /></td></tr>";
        }
        $file_string .= "</table>";
        $calendar .= '<div class="event '.$classes.'" style="background:' . $this->event_types[$event['type']]['color'] . '"><a href="event?e=' . $event['id'] . '"><b> '
                . $this->system->trimStr($event['title'], 50) . ' </b></a><div class="calendar-event-info"><span>'
                . $this->system->trimStr($event['description'], 100)
                . "</span>";
        if (count($event['files']) > 0) {
            $calendar.="<div class='calendar-event-info-files'>" . $file_string . "</div>";
        }
        $calendar.='<p style="margin:0px;padding:0px;">'.date('D, jS M - ga', strtotime($event['start'])) 
                . '</p></div></div>';
        return $calendar;
    }

    function get_events($start, $end, $limit = null) {
        if(!is_null($limit)) {
            $limit = " LIMIT ".$limit;
        }
        $sql = "SELECT * FROM event WHERE id IN"
                . "(SELECT event_id FROM event_share WHERE "
                . "group_id IN "
                . "(SELECT group_id FROM group_member WHERE user_id = :user_id) "
                . "OR user_id = :user_id) "
                . "AND `start` BETWEEN '" . $start . "' AND '" . $end . "' AND id NOT IN "
                . "(SELECT event_id FROM event_status WHERE user_id = :user_id "
                . "AND deleted = 1);" . $limit;
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":user_id" => $this->user->user_id
        ));
        $events = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($events as $key => $event){
            $events[$key] = $this->format_event($event);
        }
        return $events;
    }
    
    function getAssocFiles($event) {
        $sql = "SELECT * FROM `file` WHERE id IN(SELECT file_id FROM event_file WHERE event_id = :event_id AND visible=1);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":event_id" => $event
            ));
        $files = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach($files as $file) {
            $file = $this->files->format_file($file);
        }
        return $files;
    }

    function create_event($date, $title, $description) {
        if(!isset($date) || $date == "") {
            $date = date("Y-m-d H:00:00");
        }
        //$datetime = DateTime::createFromFormat('Y-m-d H:??:??', $date); 

        //$date = $datetime->format('g A'); 

        $this->database_connection->beginTransaction();
        $sql = "INSERT INTO event (title, description, user_id, start) "
            . "VALUES(:title, :description, :user_id, :start);";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(array(
                ":title" => $title,
                ":description" => $description,
                ":user_id" => $this->user->user_id,
                ":start" => $date
                ));
        $event_id = $this->database_connection->lastInsertId();
        $this->database_connection->commit();
        return $event_id;
    }

    function edit_event($event_id, $date, $title, $description) {
        $sql = "UPDATE event SET title=:title, description=:description, start=:start WHERE id=:event_id;";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(array(
                ":title" => $title,
                ":description" => $description,
//                ":user_id" => $this->user->user_id,
                ":start" => $date,
                ":event_id" => $event_id
                ));
        return $event_id;
    }

    function share_event($event_id, $receivers) {
        $receivers['user'][] = $this->user->user_id;
        foreach ($receivers as $key => $receiver) {
             foreach ($receiver as $single_id) {
                $sql = "INSERT INTO event_share(event_id, ".$key."_id) "
                . "VALUES(:event_id, :receiver_id);";
                $sql = $this->database_connection->prepare($sql);
                $sql->execute(array(
                    ":event_id" => $event_id,
                    ":receiver_id" => $single_id
                    ));
            }
        }
        var_dump($receivers);
    }

    function add_files_to_event($event_id, $files) {
        foreach ($files as $file) {
            $sql = "INSERT INTO event_file(event_id, file_id, user_id) "
                    . "VALUES(:event_id, :file_id, :user_id);";
            $sql = $this->database_connection->prepare($sql);
            $sql->execute(array(
                ":event_id" => $event_id,
                ":file_id" => $file,
                ":user_id" => $this->user->user_id
                ));
        }
        return $event_id;
    }

    function getEvent($event_id) {
        $event = NULL;
        $sql = "SELECT * FROM event WHERE id = :id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":id" => $event_id
            ));
        $event = $sql->fetch(PDO::FETCH_ASSOC);
        $sql = "SELECT DISTINCT * FROM `file` WHERE id IN"
                . "(SELECT file_id FROM event_file WHERE event_id = :id AND visible=1);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":id" => $event_id
            ));
        $event['files'] = $sql->fetchAll(PDO::FETCH_ASSOC);
        $event['type'] = "event"; 

        $event['receivers'] = array("user" => array(), "group" => array());

        $sql = "SELECT DISTINCT user_id, group_id, FROM event_share WHERE event_id = :event_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":event_id" => $event_id,
            ));
        $receivers = $sql->fetchAll(PDO::FETCH_ASSOC);

         foreach ($receivers as $index => $single_row) {
            foreach($single_row as $type => $entity_id) {
                if(!is_null($entity_id)) {
                    array_push($event['receivers'][strstr($type, '_', true)], $receivers[$index][$type]);
                }
            }
        }
        //$event['receivers'] = $receivers;
        return $event;
    }

    function widget($time) {
        $return = '';
        $return .= "<div class='calendar-widget'>"
                . "<div class='calendar-widget-month'>"
                . date('F', $time) . "</div>"
                . "<div class='calendar-widget-day'>"
                . "<p>" . date('d', $time) . "</p><span>" . date('l', $time) . "</span>"
                . "</div>"
                . "<div class='calendar-widget-time post_comment_text'>" . date('h:i A', $time) . "</div>"
                . "</div>";
        return $return;
    }
    
    function remove_file($event_id, $file_id) {
        $sql = "UPDATE event_file SET visible=0 WHERE event_id = :event_id AND file_Id = :file_id;";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":event_id" => $event_id,
            ":file_id" => $file_id
        ));
    }


    function delete($event_id) {
        $sql = "INSERT INTO event_status (event_id, user_id, deleted) VALUES (:event_id, :user_id, 1);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":event_id" => $event_id,
            ":user_id" => $this->user->user_id
            ));
    }

    function markDone($event_id) {
        $sql = "INSERT INTO event_status (complete, user_id, event_id)"
                . "VALUES(1,:user_id, :event_id);";
        $sql = $this->database_connection->prepare($sql);
        $sql->execute(array(
            ":event_id" => $event_id,
            ":user_id" => $this->user->user_id
        ));
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['action'])) {
        $calendar = Calendar::getInstance();
        if($_POST['action'] == "createEvent") {
            $receivers = array();
            if(isset($_POST['receivers'])) {
                $receivers = $_POST['receivers'];
            }
            $files = array();
            if(isset($_POST['files'])) {
                $files = $_POST['files'];
            }
            $calendar->share_event($calendar->add_files_to_event($calendar->create_event($_POST['date'], $_POST['title'], $_POST['description']), $files), $receivers);
        } else if($_POST['action'] == 'editEvent') {
            $receivers = array();
            if(isset($_POST['receivers'])) {
                $receivers = $_POST['receivers'];
            }
            $files = array();
            if(isset($_POST['files'])) {
                $files = $_POST['files'];
            }
            $calendar->share_event($calendar->add_files_to_event($calendar->edit_event($_POST['event_id'], $_POST['date'], $_POST['title'], $_POST['description']), $files), $receivers);
        }

        else if($_POST['action'] == "removeEventFile") {
            $calendar->remove_file($_POST['event_id'], $_POST['file_id']);
        } else if($_POST['action'] == "deleteEvent") {
            $calendar->delete($_POST['event_id']);
        } else if($_POST['action'] == "completeEvent") {
            $calendar->markDone($_POST['event_id']);
        }
    }
} else if($_SERVER['REQUEST_METHOD'] == "GET") {
    if(isset($_GET['action'])) {
        $calendar = Calendar::getInstance();
        if($_GET['action'] == 'get_events') {
            die(json_encode($calendar->get_events(date('Y:m:d 00:00:00'), date('Y:m:d H:i:s', strtotime("+1 year")), $_GET['limit'])));
        }
    }
}