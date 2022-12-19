<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'pheanstalk3/autoload.php';

if (!class_exists('PHPMailer')) {
    require_once 'phpmailer/PHPMailerAutoload.php';
}

$mailPayload = new stdClass();
$mailPayload->Host = "smtp.gmail.com";
$mailPayload->SMTPAuth = true;
$mailPayload->Username = "your-email@gmail.com"; //Enter your code here
$mailPayload->Password = "your-gmail-password"; //Enter your code here
$mailPayload->SMTPSecure = "tls";
$mailPayload->Port = 587;
$mailPayload->FromEmail = "your-email@gmail.com"; //Enter your code here
$mailPayload->FromName = "your display name";
$mailPayload->To = "customer-email@gmail.com"; //Enter your code here
$mailPayload->isHTML = true;
$mailPayload->Subject = "Test is Test Email Use Beanstalkd Queue";
$mailPayload->Body = "<b>This is a Test Email sent via Gmail SMTP Server Use Beanstalkd Queue.</b>";
$mailPayload->Callback = "UpdateSendStatus";

/**
 * Them cac param khac tuy thich, de can thiet cho worker xu ly. 
 */

$mailPayload->param_1 = "value_1";
$mailPayload->param_2 = "value_3";


$queue = new Pheanstalk\Pheanstalk('127.0.0.1');

$isContinue = false;
$handle = fopen("php://stdin", "r");

do{
    echo "Enter recipient's email. [Press Enter is default $mailPayload->To]. Press 0 to exit): ";
    $to  = trim(fgets($handle));
    if(empty($to)){
       echo "Use default email $mailPayload->To" .  PHP_EOL;
    } else if ($to==='0'){
        exit();
    }  else{
        $mailPayload->To = $to;
    }
    
    echo "Enter Subject's email. [Press Enter is default]. Press 0 to exit): ";
    $subject  = trim(fgets($handle));
    if(empty($subject)){
       echo "Use default email $mailPayload->To" .  PHP_EOL;
    } else if ($subject==='0'){
        exit();
    }  else{
        $mailPayload->Subject = $subject;
    }
    
    echo "Enter Body's email. [Press Enter is default]. Press 0 to exit): ";
    $body  = trim(fgets($handle));
    if(empty($body)){
       echo "Use default email $mailPayload->To" .  PHP_EOL;
    } else if ($body==='0'){
        exit();
    }  else{
        $mailPayload->Body = $body;
    }
    
    $queue->putInTube("smailer", json_encode($mailPayload));
    
    echo  "Your email have put in queue. Do you want to send other email? [y/n] Default=n: ";
    $yn  = trim(fgets($handle));
    $isContinue = (strtolower($yn) === 'y');
}while ($isContinue);

echo "Bye" . PHP_EOL;

fclose($handle);



