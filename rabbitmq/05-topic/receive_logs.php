<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include('../config.php');
$exchangeName = 'topic_logs';

$routingKeys = array_slice($argv, 1);

if(empty($routingKeys)) {
    exit('select log levels');
}

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();

$channel->exchange_declare($exchangeName, 'topic', false,false,false);

list($queueName, ,) = $channel->queue_declare('', false,false, false, false);

echo "[QUEUE]:{$queueName}\n\n";

// 绑定
foreach ($routingKeys as $routingKey) {
    $channel->queue_bind($queueName, $exchangeName, $routingKey);
}

$channel->basic_consume($queueName, '', false, false, false, false, function (AMQPMessage $message) {
    echo "[RECEIVE]:{$message->getBody()}\n";
    $message->get('channel')->basic_ack($message->getDeliveryTag());
});


while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->queue_delete($queueName);

$channel->close();
$connection->close();