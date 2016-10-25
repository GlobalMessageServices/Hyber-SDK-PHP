<?php

include_once 'Autoload.php';

if (!defined('HYBER_PATH_SDK')) {
    define('HYBER_PATH_SDK', dirname(__FILE__) . '/');

    \Hyber\Autoload::init();
}
