<?php
defined('BASEPATH') or exit('No direct script access allowed');
use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

// ... configure the scheduled jobs (see below) ...

// Let the scheduler execute jobs which are due.
$scheduler->run();

// ------------------------------------------------------------------------

if (!function_exists('send_mail_user_register_cronjob')) {
    /**
     * This is a convert UTC timestamp to date helper.
     * Check existed in user document and convert it
     * @param string $timestamp
     * @return	date
     */
    function send_mail_user_register_cronjob($timestamp)
    {
        
    }
}

// ------------------------------------------------------------------------
