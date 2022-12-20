<?php
use TheSeer\Tokenizer\Exception;
/**
 * Form rules for validate form requests
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

if (!function_exists('hook_view')) {
    /**
     * This is a hook between the header and body html.
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return	boolean
     */
    function hook_view(string $view, array $data = null)
    {
        $CI = & get_instance();

        // validate params
        if (empty($view))
            throw new Exception('Argument $view must not empty');

        $CI->load->view('commons/headHtml');
        $CI->load->view($view, $data);
        $CI->load->view('commons/bodyHtml');
    }
}

// ------------------------------------------------------------------------
