<?php

namespace Kszkl\Calendar\Admin;

use \Kszkl\Calendar\Base\BaseController;

class AdminPages extends BaseController
{
    public function register(){
        add_action('admin_menu', [$this, 'add_pages']);
    }
    
    public function add_pages(){
        add_menu_page("Events List", "Calendar", "edit_others_posts", "calendar_plugin", [$this, 'admin_events'], 'dashicons-calendar-alt');
        add_submenu_page('calendar_plugin', 'Events List', 'Events', 'edit_others_posts', 'calendar_plugin', [$this, 'admin_events']);
        add_submenu_page('calendar_plugin', 'Create Event', 'Create', 'edit_others_posts', 'calendar_plugin_create', [$this, 'admin_create']);
    }

    public function admin_events(){
        require_once $this->plugin_path . "templates/admin-events.php";
    }

    public function admin_create(){
        require_once $this->plugin_path . "templates/admin-create.php";

    }
}