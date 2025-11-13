<?php

namespace Kszkl\Calendar\Base;

class DbService
{
    private $db;
    private $table;
    private $instances_table;
    private $table_suffix = "calendar_events";
    private $instances_table_suffix = "calendar_events_instances";

    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . $this->table_suffix;
        $this->instances_table = $this->db->prefix . $this->instances_table_suffix;
    }

    /**
     * creates the table if  not existing
     * @return void
     */
    public function register(){
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
    
        $charset_collate = $this->db->get_charset_collate();

        $sql = "CREATE TABLE {$this->table} (
        id  BIGINT(20) NOT NULL AUTO_INCREMENT,
        type ENUM('lesson', 'blocking') NOT NULL,
        event_name VARCHAR(255) NOT NULL,
        teacher VARCHAR(255),
        class VARCHAR(4),
        room INT(3),
        start_time DATETIME NOT NULL,
        whole_day BOOLEAN NOT NULL,
        end_time DATETIME,
        recurrence_type ENUM('none', 'daily', 'weekly', 'biweekly', 'monthly', 'yearly') NOT NULL,
        recurrence_end DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
        ) $charset_collate;";
                
        $sql_instances  = "CREATE TABLE {$this->instances_table}(
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        event_id BIGINT(20) NOT NULL,
        start_time DATETIME,
        end_time DATETIME,
        is_blocked BOOLEAN NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (event_id) REFERENCES {$this->table}(id)
        ) $charset_collate;";
  
        if(! $this->table_exists($this->table)){
            dbDelta($sql);
        }
  
        if(! $this->table_exists($this->instances_table)){
            dbDelta($sql_instances);
        }
    }


    /**
     * creates a new event in the calendar_events table
     * @param array{
     *      type: 'lesson'|'blocking',
     *      event_name: string,
     *      teacher: string,
     *      class: string,
     *      room: int,
     *      start_time: string,
     *      whole_day: bool,
     *      end_time: string,
     *      reccurence_type: 'none'|'daily'|'weekly'|'biweekly'|'monthly'|'yearly',
     *      recurrence_end: string
     *  } $args
     * @return bool
     */
    public function create_new_event(array $args){

        if($args['whole_day'] === 0 && ! $args['end_time']){
            error_log("'end_time' param is required if 'whole_day' is False");
            return False;
        }
        
        $result = $this->db->insert($this->table, $args);
        return $result !== false;
    }

    /**
     * creates a new event instance in the calendar_events_instances table
     * @param array{
     *      event_id: int,
     *      event_type: 'lesson'|'blocking',
     *      start_time: \DateTime,
     *      end_time: \DateTime,
     *  } $args
     * @return bool
     */
    public function create_new_event_instance(array $args){
        
        $formatted_start_time = $args['start_time']->format('Y-m-d H:i:s');
        $formatted_end_time = $args['end_time']->format('Y-m-d H:i:s');
        
        if($args['type'] === "blocking"){
            $this->block_all_overlapping_instances($formatted_start_time, $formatted_end_time);
            $is_blocked = 0;
        }else{
            $is_blocked = $this->check_if_blocked($formatted_start_time,$formatted_end_time);
        }

        $result = $this->db->insert($this->instances_table, [
            'event_id' => $args['event_id'],
            'start_time' => $formatted_start_time,
            'end_time' => $formatted_end_time,
            'is_blocked' => $is_blocked
        ]);

        return $result !== false;
    }
    
    /**
     * checks if a blocking event exists for the start_time of a given instance
     * @return bool
     */
    private function check_if_blocked($instance_start_time, $instance_end_time){
        $sql = $this->db->prepare("SELECT i.id FROM {$this->instances_table} i JOIN {$this->table} e ON i.event_id = e.id WHERE type='blocking' AND  ( (i.start_time <= %s AND %s < i.end_time) OR (i.start_time < %s AND %s <= i.end_time) OR (i.start_time >= %s AND i.end_time <= %s.));", $instance_start_time, $instance_start_time, $instance_end_time, $instance_end_time, $instance_start_time, $instance_end_time);
        $result = $this->db->get_var($sql);
        return $result ? True: False;
    }

    /**
     * block all of the instances overlapping with the given event
     * @return bool
     */
    private function block_all_overlapping_instances($instance_start_time, $instance_end_time){
        $sql = $this->db->prepare("UPDATE {$this->instances_table} SET is_blocked = 1 WHERE id IN ( SELECT i.id FROM {$this->instances_table} i JOIN {$this->table} e ON i.event_id = e.id WHERE type='lesson' AND  ( (i.start_time >= %s AND i.start_time < %s) OR (i.end_time > %s AND i.end_time <= %s) OR (%s >= i.start_time AND %s <= i.end_time));", $instance_start_time, $instance_end_time, $instance_start_time, $instance_end_time, $instance_start_time, $instance_end_time);
        $result = $this->db->query($sql);
        return !($result === 'false');
    }

    /**
     * Returns all recurring events from events_table if recurrence_end > now ( or null )
     * @return object[] array of objects containing (id, type, event_name, teacher, class, room, start_time, whole_day, end_time, recurrence_type, recurrence_end)
     */
    public function get_all_recurring_events(){
        $rows = $this->db->get_results("SELECT id, type, event_name, teacher, class, room, start_time, whole_day, end_time, recurrence_type, recurrence_end FROM {$this->table} WHERE recurrence_type != none AND (recurrence_end > NOW() OR recurrence_end IS NULL);");
        return $rows;
    }


    /**
     * Returns the start_time of the last instance of the given event_id
     * @param integer $event_id
     * @return string|null
     */
    public function get_last_instance_of_event($event_id){
        $sql = $this->db->prepare("SELECT start_time FROM {$this->instances_table} WHERE event_id = %n ORDER BY id DESC LIMIT 1;", $event_id);
        $start_time = $this->db->get_var($sql);
        return $start_time;
    }

    /**
     * checks whether the table exists
     * @param string $table_name
     * @return bool
     */
    private function table_exists(string $table_name){
        $sql = $this->db->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        );
        return $this->db->get_var( $sql ) === $table_name;
    }
}
