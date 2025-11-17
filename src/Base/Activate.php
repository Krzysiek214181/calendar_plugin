<?php

namespace Kszkl\Calendar\Base;

use Kszkl\Calendar\Base\DbService;
use Kszkl\Calendar\Base\EventGenerator;

class Activate
{
    public static function activate(){
        $dbService = new DbService();
        $dbService->register();
        $eventGenerator = new EventGenerator();
        $eventGenerator->generate_events();
    }
}