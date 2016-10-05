# Hyber-SDK-PHP
Easy way to integrate PHP-powered system with Hyber platform

[![Build Status](https://travis-ci.org/Incuube/Hyber-SDK-PHP.svg)](https://travis-ci.org/Incuube/Hyber-SDK-PHP)

## Installaton

#### Recommended: composer
 
      composer require incuube/sdk-hyber

#### Not recommended: download sources

      wget https://github.com/Incuube/Hyber-SDK-PHP/archive/master.zip
      
Include autoload SDK:

      include_once "sdk-hyber/Hyber/bootstrap.php";
      
## Usage example
```PHP
// First, you need choose Http Client
// We recommend, but you can choose which one you like best

    composer require guzzlehttp/guzzle
    composer require php-http/guzzle6-adapter

$config = [
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ],
    'auth' => [
        $login,
        $password,
    ],
];

$guzzle = new GuzzleHttp\Client($config);
$adapter = new Http\Adapter\Guzzle6\Client($guzzle);
$apiClient = new \Hyber\ApiClient($adapter, new \Http\Message\MessageFactory\GuzzleMessageFactory());

// Second, you need to create service that will send your messages.
// All this parameters are mandatory. They are provided for each Hyber customer and rarely change  
$sender = new Hyber\MessageSender($apiClient, $identifier, $alphaName);
// You may specify some additional sender parameters, however they are not mandatory
$sender->setCallbackUrl($config->getDRReceiverUrl());

// Third, you need to create and configure message instance
// Parameters in constructor are mandatory, parameters in setters are not
$message = new Hyber\Message($phoneNumber);
$message->setExtraId($mySystem->getMessageId()); //some identifier from external system
$message->setTag('campaign'); //on Hyber portal you can filter statistics by tag
$message->setIsPromotional(true); //whether or not your message is promotopnal (advertising)

// Fourth, you need to configure channels with which your message will be sent
// You may add whatever available channels you want, however if specific channel is not enabled for you,
// there will be no delivery via this channel
 
// For each channel mandatory parameters are text for this channel and TTL
// (time-to-live, how long we try to send message via this channel before considering it expired)
$pushMessage = new Hyber\Message\Push('Text for push', static::TTL_PUSH);
//each channel also can have some specific parameters
$pushMessage->addImage($imageUrl);
$pushMessage->addButton($buttonCaption, $buttonLink);
$message->addPush($pushMessage);

// Channels will be used in same order you added them
// It is recommended to add channels in same order as in this example - this is a cheapest option
$viberMessage = new Hyber\Message\Viber('Text for Viber', static::TTL_VIBER);
$viberMessage->addImage($imageUrl);
$viberMessage->addButton($buttonCaption, $buttonLink);
$viberMessage->addIosExpirityText('Ios Expirity Text');
$message->addViber($viberMessage);

$smsMessage = new Hyber\Message\Sms('Text for SMS', static::TTL_SMS);
$message->addSms($smsMessage);

// Now you can send your message. Second parameter is optional,
// it represents when to start message processing
$response = $sender->send($message, new \DateTime('+1 hour'));

// You may receive SuccessResponse...
if ($response instanceof Hyber\Response\SuccessResponse) {
    echo $response->getMessageId();
// ... or ErrorResponse
} elseif ($response instanceof Hyber\Response\ErrorResponse) {
    echo $response->getHttpCode(); // HTTP code is always present
    echo $response->getErrorCode(); // error code may be null in some cases
    echo $response->getErrorText(); // error text may be null in some cases
}

// However, if the message was not sent at all -
// you will receive exception from Guzzle(transport layer)
```
