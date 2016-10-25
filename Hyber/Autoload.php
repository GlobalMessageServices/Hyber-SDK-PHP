<?php

namespace Hyber;

class Autoload
{
    public static function init()
    {
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }

        return spl_autoload_register(array('\Hyber\Autoload', 'load'));
    }

    public static function load($className)
    {
        $className = str_replace('Hyber\\', '', $className);
        $className = HYBER_PATH_SDK . $className . '.php';
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

        if ((file_exists($className) === false) || (is_readable($className) === false)) {
            return false;
        }

        require($className);

        return true;
    }
}
