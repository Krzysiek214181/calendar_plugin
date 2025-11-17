<?php

namespace Kszkl\Calendar;

class Init
{
    /**
     * returns all of the predefined services that should be instantiated
     * @return string[]
     */
    public static function getServices(){
        return [
            Admin\AdminLinks::class,
            Admin\AdminPages::class,
            Base\EventCreationHandler::class,
            Base\EventGenerator::class,
            Base\EventRestApi::class
        ];
    }

    /**
     * calls the register function on all of the classes defined in the getServices() function
     * @return void
     */
    public static function register_services(){
        foreach (self::getServices() as $class){
            $service = self::instantiate($class);
            if ( method_exists($service, 'register')){
                $service->register();
            }
        }
    }

    private static function instantiate($class){
        return new $class;
    }
}