<?php

/*
Plugin Name: Calendar Plugin
Plugin Uri: https://github.com/Krzysiek214181/calendar_plugin
Description:
Author: Krzysztof Szklarski, Tomasz Sykuła
Author Uri: www.github.com/vkiuu
Licence: GPLv2 or later
*/

/*
Calendar Plugin
Copyright (C) 2025 Krzysztof Szklarski, Tomasz Sykuła

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see
<https://www.gnu.org/licenses/>. 
*/

defined('ABSPATH') or die('');

if ( file_exists(dirname(__FILE__) . '/libs/autoload.php')){
    require_once dirname(__FILE__) . '/libs/autoload.php';
}

if( class_exists('Kszkl\\Calendar\\Init') ){
    Kszkl\Calendar\Init::register_services();
}

function activate_calendar_plugin(){
    Kszkl\Calendar\Base\Activate::activate();
}

register_activation_hook(__FILE__, 'activate_calendar_plugin');