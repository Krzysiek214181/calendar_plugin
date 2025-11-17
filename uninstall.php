<?php

if( !defined('WP_UNINSTALL_PLUGIN')){
    die;
}

global $wpdb;

$event_table_name = $wpdb->prefix . 'calendar_events';
$event_instances_table_name = $wpdb->prefix .'calendar_events_instances';
$wpdb->query("DROP TABLE IF EXISTS $event_table_name");
$wpdb->query("DROP TABLE IF EXISTS $event_instances_table_name");