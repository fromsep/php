<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.56.101', 5672, 'admin', 'admin', 'my_vhost');
$channel = $connection->channel();

$channel->queue_declare('simple_queue', false, false,false, false,false);

$msg = 'hello';
$message = new AMQPMessage($msg);
$channel->basic_publish($message, '', 'simple_queue');

echo "[PRODUCT] '{$msg}'\n";

$connection->close();