<?php

/*
 * Copyright © 2014 South Telecom
 * HOWTO RUN:
 * 1.Start: php <path_to_dir>/worker-email-smmailer.php
 * 2.Stop: xóa file <path_to_dir>/worker-emmail-smailer.pid 
 *   - Windows: Del /f "<path_to_dir>/worker-emmail-smailer.pid"
 *   - Linux: rm -f "<path_to_dir>/worker-emmail-smailer.pid"
 *  
 * 
 */
require_once 'pheanstalk3/autoload.php';

if (!class_exists('PHPMailer')) {
    require_once 'phpmailer/PHPMailerAutoload.php';
}

//echo 'khoa' . __DIR__;
// chdir('..');
// echo realpath('');
// die;

require_once realpath('myconfig.php');
//require_once '/../../../yourconfig.php';
require_once __DIR__ . '/Mongo_db.php';

$mongo_db = new Mongo_db();

function UpdateSendStatus($mailPayload) {
    echo PHP_EOL . "At " . date('c',time()) . PHP_EOL;
    echo json_encode($mailPayload, JSON_PRETTY_PRINT) . PHP_EOL;
    /*
     * Muon xu ly gi voi $mailPayload thi xu ly.
     */

    global $mongo_db;
    print_r($mongo_db->get('user'));
    echo "----------------" . PHP_EOL ;
}

$WATCHTUBE = BEANSTALKD_USER_VERIFY_REGISTER_TUBE;
$queue = new Pheanstalk\Pheanstalk("127.0.0.1"); // OR IP Address of Server running beanstalkd

$PIDFILE = __DIR__ . "/worker-send-otp.pid";

touch($PIDFILE);

echo "Worker " . __FILE__ . " have started. To exit, delete pid file  " .  $PIDFILE . PHP_EOL;
$count = 0;
while (file_exists($PIDFILE)) {
    echo $count++;
    while ($job = $queue->reserveFromTube($WATCHTUBE, 15)) {
        try {
            $mailPayload = json_decode($job->getData(), false);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = EMAIL;                 // SMTP username
            $mail->Password = EMAIL_PASSWORD;                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // 587;                                    // TCP port to connect to
            $mail->CharSet = 'utf-8';

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            $mail->setFrom(EMAIL, 'Document Sharing');
            // receiver
            $mail->addAddress($mailPayload->email);

            // Name is optional
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Document Sharing - Xác minh đăng ký';
            $mail->Body = $mailPayload->message;
            
            /** Test queue and send email 
            $mailPayload->SendResult = $mail->send();
            if (!$mailPayload->SendResult) {
                $mailPayload->ErrorInfo = $mail->ErrorInfo;
            }
            $mailPayload->SendTimestamp = time();
            $mail->smtpClose();
            /*
             */
            
            //** Test queue, do not send email 
            $mailPayload->SendResult = true;
            $mailPayload->SendTimestamp = time();
            /* 
             */
            
            //Excute Callback function
            if (property_exists($mailPayload, 'Callback')) {
                if (function_exists($mailPayload->Callback)) {
                    call_user_func($mailPayload->Callback, $mailPayload);
                }
            }
            
            //End Callback function  
            $queue->delete($job);
        } catch (Exception $e) {
            $jobData = $job->getData();
            $queue->delete($job);
            var_dump($e);
            //If day job vao lai
            $queue->useTube($WATCHTUBE)->put($jobData);
            exit();
        }
        if(!file_exists($PIDFILE)){
            exit();
        }
    }
}

