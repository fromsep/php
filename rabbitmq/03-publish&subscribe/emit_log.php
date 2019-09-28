<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include('../config.php');
$exchangeName = 'logs';

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();

$channel->exchange_declare($exchangeName, 'fanout', false,false,false);

$i = 0;
while(true) {
    $msg = '[log]' . date('Y-m-d H:i:s') . "[{$i}]";

    $message = new AMQPMessage($msg, [
        'deliver_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);

    $channel->basic_publish($message, $exchangeName);

    echo "[SEND]:{$msg}\n";
    sleep(1);
    $i++;
}

$channel->close();
$connection->close();