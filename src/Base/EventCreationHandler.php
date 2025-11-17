<?php

namespace Kszkl\Calendar\Base;

use Kszkl\Calendar\Base\DbService;
use Kszkl\Calendar\Base\EventGenerator;

class EventCreationHandler
{
    private $dbService;
    private $eventGenerator;

    public function __construct(){
        $this->eventGenerator = new EventGenerator();
        $this->dbService = new DbService();
    }
    public function register(){
        add_action("admin_post_calendar_event_creation_submit", [$this, "handle_event_creation"]);
    }

    /**
     * handles the creation of new events
     * @return void
     */
    public function handle_event_creation(){
        if(!isset($_POST['calendar-event-creation-nonce']) || !wp_verify_nonce($_POST['calendar-event-creation-nonce'], 'calendar-event-creation-form')){
            wp_die('Security check failed');
        }
        //changing start_time and end_time to 00:00 and 23:59 accordingly if whole day = 1         
        if($_POST['whole_day']){
            $start_time = '00:00';
            $end_time = '23:59';
        }else{
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
        }
        
        $event_id = $this->dbService->create_new_event([
            'type' => sanitize_text_field($_POST['type']),
            'event_name' => sanitize_text_field($_POST['event_name']),
            'teacher' => sanitize_text_field($_POST['teacher']),
            'class' => sanitize_text_field($_POST['class']),
            'room' => intval($_POST['room']),
            'start_time' => sanitize_text_field($_POST['start_date']) . " " . sanitize_text_field($start_time),
            'whole_day' =>boolval($_POST['whole_day']),
            'end_time' => sanitize_text_field($_POST['end_date']) . ' ' . sanitize_text_field($end_time),
            'recurrence_type' => sanitize_text_field($_POST['recurrence_type']),
            'recurrence_end' => sanitize_text_field($_POST['recurrence_end'])
        ]);

        if($event_id !== false){
            $event_object = (object) [
                'id'=>$event_id,
                'type'=>sanitize_text_field($_POST['type']),
                'start_time' => sanitize_text_field($_POST['start_date']) . " " . sanitize_text_field($start_time),
                'end_time' => sanitize_text_field($_POST['end_date']) . ' ' . sanitize_text_field($end_time),
                'recurrence_type' => sanitize_text_field($_POST['recurrence_type']),
                'recurrence_end' => sanitize_text_field($_POST['recurrence_end'])
            ];
            $this->eventGenerator->generate_events([$event_object]);
        }

        $url = $event_id ? add_query_arg('submition_status', 'success', wp_get_referer()) : add_query_arg('submition_status', 'fail', wp_get_referer());

        wp_redirect($url);
        exit;
    }
}