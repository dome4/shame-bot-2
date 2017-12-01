<?php
require __DIR__ . "/vendor/autoload.php";

use React\EventLoop;
use Slack\RealTimeClient;
use Slack\Message\Attachment;
use Slack\Channel;

// include config file
$config = include('config.php');

$loop = EventLoop\Factory::create();

// Slack RTM API
$rtmClient = new RealTimeClient($loop);
$rtmClient->setToken($config['slack-token']);

// Slack Web API
$webAPIClient = new \Slack\ApiClient($loop);
$webAPIClient->setToken($config['slack-token']);


$rtmClient->on('message', function ($recMessage) use ($rtmClient, $webAPIClient) {

    // handle every message which has more than 10 characters and reply on the same channel
    if(strlen($recMessage['text']) >= 10 && $recMessage['bot_id'] == ''){


        $webAPIClient->getChannelById($recMessage['channel'])->then(function (Channel $channel) use ($webAPIClient, $recMessage) {

            // attachement
            $data = [
                'fallback' => 'Shame on you!',
                'color' => '#FF0000',
                'image_url' => 'https://pbs.twimg.com/media/Cqu5MhRWcAEz0Wk.jpg'
            ];

            // new message
            $newMessage = $webAPIClient->getMessageBuilder()
                ->setText('Shame on you, <@' .$recMessage['user']. '>!')
                ->setChannel($channel)
                ->addAttachment(Attachment::fromData($data))
                ->create();

            // send response
            $webAPIClient->postMessage($newMessage);
        });
    }
});

// Connect to the Slack server.
$rtmClient->connect()->then(function () {
    echo "ShameBot connected!\n";
});


// Run the event loop.
$loop->run();