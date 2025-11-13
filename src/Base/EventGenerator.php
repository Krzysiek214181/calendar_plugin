<?php

namespace Kszkl\Calendar\Base;

use DateTime;
use \Kszkl\Calendar\Base\DbService;

class EventGenerator
{
    private $DbService;

    public function __construct(){
        $this->DbService = new DbService();
    }

    public function register(){
        $timestamp = strtotime("tommorow midnight");
        if(!wp_next_scheduled("calendar_generate_events_cron")){
            wp_schedule_event($timestamp, "daily", "calendar_generate_events_cron");
        }

        add_action("calendar_generate_events_cron", [$this, "generate_events"]);
    }
    
    /**
     * generate events for the following month
     * @return void
     */
    public function generate_events(){
        $events = $this->DbService->get_all_recurring_events();
        $generation_window = new DateTime("now");
        $generation_window->modify("+1 month");
        
        foreach($events as $event){
            $recurrence_interval = $this->rec_type_to_time_mod($event->recurrence_type);
            // find the event start time (either last instance's start_time + reccurence type or start_time from scheme event table)
            $last_instance = $this->DbService->get_last_instance_of_event($event->id);
            if($last_instance){
                $last_instance_start_time = new DateTime($last_instance);
                $next_instance_start_time = $last_instance_start_time->modify($recurrence_interval);
            }else{
                $next_instance_start_time = new DateTime($event->start_time);
            }

            //while not exceeding the generation_window or the recurrence_window create instance and move the next_instance_start_time by the recurrence_interval
            while($next_instance_start_time < $generation_window || ($event->recurrence_end && $next_instance_start_time < $event->recurrence_end) ){
                //
                $next_instance_start_time->modify($recurrence_interval);
            }
        }
    }

    /**
     * converts the giver recurrence type into a valid DateTime modifier
     * @param string $recurrence_type
     * @return string
     */
    private function rec_type_to_time_mod($recurrence_type){
        $recurrence_map = [
            "daily"    => "+1 day",
            "weekly"   => "+1 week",
            "biweekly" => "+2 weeks",
            "monthly"  => "+1 month",
            "yearly"   => "+1 year",
        ];

        return $recurrence_map[$recurrence_type];
    }
}