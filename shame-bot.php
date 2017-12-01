<?php
require __DIR__ . "/vendor/autoload.php";

use React\EventLoop;
use Slack\RealTimeClient;

// include config file
$config = include('config.php');



$loop = EventLoop\Factory::create();

$rtmClient = new RealTimeClient($loop);
$rtmClient->setToken($config['slack-token']);

// disconnect after first message
$rtmClient->on('message', function ($data) use ($rtmClient) {
    echo "Someone typed a message: ".$data['text']."\n";
    $rtmClient->disconnect();
});

// Connect to the Slack server.
$rtmClient->connect()->then(function () {
    echo "Connected!\n";
});


// Run the event loop.
$loop->run();