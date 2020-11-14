<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include('../config.php');
$queueName = 'task_queue';

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();


printf("[%19s] %s\t %s\t %s\n", 'date', 'queue_name', 'task_count', 'worker_count');
while(true) {
    $declare = $channel->queue_declare($queueName, false, true, false, false);
    printf("[%s] %s\t %s\t %s\n", date('Y-m-d H:i:s'), $declare[0], $declare[1], $declare[2]);
    sleep(3);
}

$channel->close();
$connection->close();