<?php
use TheSeer\Tokenizer\Exception;
/**
 * Form rules for validate form requests
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

if (!function_exists('send_mail')) {
    /**
     * This is a sending mail helper.
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return	boolean
     */
    function send_mail($to, $message, $subject = 'Document Sharing - Xác minh đăng ký')
    {
        $CI = & get_instance();

        // validate params
        if (empty($message))
            throw new Exception('Variable $message must not empty');

        $CI->load->helper('form_rules');
        $CI->load->helper(array('form', 'url'));
        $CI->load->library('form_validation');
        $CI->form_validation->set_data(['to' => $to]);
        $CI->form_validation->set_rules(form_mail_send());
        if ($CI->form_validation->run() == FALSE) {
            throw new Exception(form_error('to'));
        }

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_user' => $CI->config->item('my_email'),
            'smtp_pass' => $CI->config->item('my_email_password'),
            'smtp_port' => 465,
            'smtp_timeout' => 5,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            //'validate' => true,
        );

        $CI->load->library('email', $config); // load library 
        // $CI->email->initialize($config);
        $CI->email->from($CI->config->item('my_email'), 'Document Sharing');

        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);

        return $CI->email->send();
    }
}

// ------------------------------------------------------------------------
