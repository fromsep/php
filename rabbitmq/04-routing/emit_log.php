<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include('../config.php');
$exchangeName = 'direct_logs';
$levels = ['info', 'warning', 'error'];

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();

$channel->exchange_declare($exchangeName, 'direct', false,false,false);

$i = 0;
while(true) {
    $logLevel = $levels[mt_rand(0, 2)];
    $msg = "[$logLevel]" . date('Y-m-d H:i:s') . "[{$i}]";

    $message = new AMQPMessage($msg, [
        'deliver_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);

    $channel->basic_publish($message, $exchangeName, $logLevel);

    echo "[SEND]:{$msg}\n";
    sleep(1);
    $i++;
}

$channel->close();
$connection->close();