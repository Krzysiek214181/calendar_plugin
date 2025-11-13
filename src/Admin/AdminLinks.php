<?php

namespace Kszkl\Calendar\Admin;

use Kszkl\Calendar\Base\BaseController;

class AdminLinks extends BaseController
{
    public function register(){
        add_filter( "plugin_action_links_" . $this->plugin_name, [$this, 'add_links']);
    }

    public function add_links($links){
        $view_link = '<a href="admin.php?page=calendar_plugin">View Events</a>';
        $create_link = '<a href="admin.php?page=calendar_plugin_create">Create Events</a>';
        array_push( $links, $view_link, $create_link);
        return $links;
    }
}