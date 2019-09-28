<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.56.101', 5672, 'admin', 'admin', 'my_vhost');
$channel = $connection->channel();

$channel->queue_declare('simple_queue', false, false,false, false,false);

$queueName = 'simple_queue';

$channel->basic_consume($queueName, '', false, true, false,false, 'callback');

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();

function callback($message) {
    echo "[CONSUME] {$message->body}\n";
}