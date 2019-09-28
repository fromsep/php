<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include('../config.php');
$exchangeName = 'topic_logs';
$levels = ['info', 'warning', 'error'];
$apps   = ['www', 'm' , 'api'];

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();

$channel->exchange_declare($exchangeName, 'topic', false,false,false);

$i = 0;
while(true) {
    $routingKey = $apps[mt_rand(0, 2)] . '.' . $levels[mt_rand(0, 2)];
    $msg = "[$routingKey]" . date('Y-m-d H:i:s') . "[{$i}]";

    $message = new AMQPMessage($msg, [
        'deliver_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);

    $channel->basic_publish($message, $exchangeName, $routingKey);

    echo "[SEND]:{$msg}\n";
    sleep(1);
    $i++;
}

$channel->close();
$connection->close();