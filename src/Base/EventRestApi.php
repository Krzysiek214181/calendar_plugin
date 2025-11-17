<?php

namespace Kszkl\Calendar\Base;

use Kszkl\Calendar\Base\DbService;

class EventRestApi
{
    private $events;
    private $dbService;

    public function __construct(){
        $this->dbService = new DbService();
        $this->update_events_instances();
    }

    public function register(){
        add_action('rest_api_init', function() {
            register_rest_route(
                'kszts/calendar',
                '/getEvents',
                [
                    'methods' => 'GET',
                    'callback' => [$this, 'return_events_instances'],
                    'permission_callback' => '__return_true'
                ]
                );
        });
        add_action('kszts_calendar_instances_updated', [$this, 'update_events_instances']);
    }

    public function update_events_instances(){
       $this->events = $this->dbService->get_all_formatted_instances();
    }

    public function return_events_instances(){
        return $this->events;
    }
}