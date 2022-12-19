<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'pheanstalk3/autoload.php';

$queue = new Pheanstalk\Pheanstalk('127.0.0.1');

$queue->useTube("tube1")->put("Hello 1.1");
$queue->useTube("tube1")->put("Hello 1.2");
$queue->useTube("tube2")->put("Hello 2.1");
$queue->useTube("tube2")->put("Hello 2.2");
$queue->useTube("tube2")->put("Hello 2.3");

while ($job = $queue->watch('tube1')->watch('tube2')->reserve(1)){
    echo $job->getData() . PHP_EOL;
    $queue->delete($job);
}

$queue->useTube("tube1")->put("Hello 1.1");
$queue->useTube("tube1")->put("Hello 1.2");
$queue->useTube("tube2")->put("Hello 2.1");
$queue->useTube("tube2")->put("Hello 2.2");
$queue->useTube("tube2")->put("Hello 2.3");

while ($job = $queue->watchOnly('tube1')->reserve(1)){
    echo $job->getData() . PHP_EOL;
    $queue->delete($job);
}

while ($job = $queue->watchOnly('tube2')->reserve(1)){
    echo $job->getData() . PHP_EOL;
    $queue->delete($job);
}

