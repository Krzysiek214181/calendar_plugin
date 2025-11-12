<?php

namespace Kszkl\Calendar\Base;

use Kszkl\Calendar\Base\DbService;

class Activate
{
    public static function activate(){
        $dbService = new DbService();
        $dbService->register();
    }
}