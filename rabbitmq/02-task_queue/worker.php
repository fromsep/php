<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include('../config.php');
$queueName = 'task_queue';

$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();
$channel->queue_declare($queueName, false, true, false,false);

echo "worker work start\n";

$channel->basic_qos(null, 1, null);

$channel->basic_consume($queueName, '', false, false, false, false, function(AMQPMessage $message) {
    echo "[CONSUME]:{$message->getBody()}\n";
    $message->delivery_info['channel']->basic_ack($message->getDeliveryTag());
    sleep(1);
});

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();