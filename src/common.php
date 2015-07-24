<?php

use Phat\Core\Configure;

define('SECOND', 1);
define('MINUTE', 60);
define('HOUR', 3600);
define('DAY', 86400);
define('WEEK', 604800);
define('MONTH', 2592000);
define('YEAR', 31536000);

if(!function_exists('debug')) {
    // TODO : Remove that require when digitalnature/php-ref merges the pull request
    require 'vendor/lo-x/php-ref/ref.php';

    /**
     * Enhanced var_dump or print_r, this function will help you debug your variables
     * This function will have no effect in production mode
     * Uses digitalnature/php-ref lib
     *
     * @param mixed $object The entity you want to debug, it can be of any type
     * @param bool  $html   If set to true, will add html/css to the output. If set to false, only outputs markdown/text
     */
    function debug($object, $html = true) {
        // If not in dev mode, return
        if(!Configure::read('debug')) {
            return;
        }

        if($html) {
            r($object);
        } else {
            echo '<pre>';
            rt($object);
            echo '</pre>';
        }
    }
    Ref::config('backtraceIndirection', 3);

    /**
     * @return boolean Returns true if in dev/debug mode, false if in production mode
     */
    function dev() {
        return Configure::read('debug');
    }

}