<?php
/**
 * w-slow Model
 *
 * @package w-slow
 * @copyright (C) 2010 John Cox
 *
 * @subpackage Index
 */

    /* This is the only configuration that you need to do.
     * Please set the following information with a MySQL user
     * That has appropriate permissions to SELECT, TRUNCATE and
     * SET GLOBALs
     */

    // SET HERE
    // Username
    $dbuser = 'root';
    // Password
    $dbpass = 'root';
    // HOST
    $dbhost = 'localhost';

    /* No need to edit anything else.  */


    include_once 'api/w-slow.php';
    $w = new wSlow($dbuser, $dbpass, $dbhost);

    // Ensure the slow log is set and running.
    $w->setSlowLog();

    // Determine what we are displaying
    if (isset($_GET['phase'])){
        $phase = htmlspecialchars($_GET['phase']);
    } elseif (isset($_POST['phase'])){
        $phase = htmlspecialchars($_POST['phase']);
    } else {
        $phase = 'display_log';
    }

    // Display
    switch(strtolower($phase)) {

        case 'display_log':
            $sl = $w->getSlowLog();
            if ($sl){

                foreach ($sl as $key => $var){
                    if ( (preg_match("/\SELECT\b/i", $var['query'])) AND (!empty($var['db'])) ){
                        $sl[$key]['id'] = 'query_'.$key;
                        $sl[$key]['explain'] = $w->explainQuery($var['db'], $var['query']);
                    } else {
                        unset($sl[$key]);
                    }
                 }
             }

             include_once 'templates/display_log.tpl.php';
        break;

        case 'truncate_log':

            $truncate = $w->truncateSlowLog();
            if (!$truncate){
                echo 'Unable to truncate the slow log.  Please check your database permissions for this user.';
            } else {
                echo 'MySQL Slow Log is empty.';
            }

        break;

    }
?>