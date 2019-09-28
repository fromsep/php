<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// https://github.com/php-amqplib/php-amqplib

$config = include('../config.php');
$queueName = 'simple_queue';

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();

$channel->set_ack_handler(function (AMQPMessage $message) {
    echo "Message acked with content " . $message->getBody() . PHP_EOL;
});

$channel->set_nack_handler(function (AMQPMessage $message) {
    echo "Message nacked with content " . $message->getBody() . PHP_EOL;
});

$channel->confirm_select();

$channel->queue_declare($queueName, false, false,false, false,false);

$msg = 'hello';

$message = new AMQPMessage($msg, [
    'deliver_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
]);

$channel->basic_publish($message, '', $queueName);

$channel->wait_for_pending_acks();

echo "[PRODUCT] '{$msg}'\n";

$connection->close();