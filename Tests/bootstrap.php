<?php

date_default_timezone_set('Europe/Prague');

require_once('vendor/autoload.php');

/*
 * Converts E_RECOVERABLE_ERROR to exception.
 * - used for tests relying on type hinting exceptions
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if ($errno === E_RECOVERABLE_ERROR) {
        throw new \Exception($errstr, $errno);
    }

    return false;
});
