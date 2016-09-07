# Hyber-SDK-PHP
Easy way to integrate PHP-powered system with Hyber platform

## Usage example
```PHP
// First, you need to create service that will send your messages.
// All this parameters are mandatory. They are provided for each Hyber customer and rarely change  
$sender = new Hyber\MessageSender($login, $password, $identifier, $alphaName);
// You may specify some additional sender parameters, however they are not mandatory
$sender->setCallbackUrl($config->getDRReceiverUrl());

// Second, you need to create and configure message instance
// Parameters in constructor are mandatory, parameters in setters are not
$message = new Hyber\Message($phoneNumber);
$message->setExtraId($mySystem->getMessageId()); //some identifier from external system
$message->setTag('campaign'); //on Hyber portal you can filter statistics by tag
$message->setIsPromotional(true); //whether or not your message is promotopnal (advertising)

// Third, you need to configure channels with which your message will be sent
// You may add whatever available channels you want, however if specific channel is not enabled for you,
// there will be no delivery via this channel
 
// For each channel mandatory parameters are text for this channel and TTL
// (time-to-live, how long we try to send message via this channel before considering it expired)
$pushMessage = new Hyber\Message\Push('Text for push', static::TTL_PUSH);
//each channel also can have some specific parameters
$pushMessage->setImage($imageUrl);
$pushMessage->setCaption($textForPushButton);
$pushMessage->setAction($linkForPushButton);
$message->addPush($pushMessage);

// Channels will be used in same order you added them
// It is recommended to add channels in same order as in this example - this is a cheapest option
$viberMessage = new Hyber\Message\Viber('Text for Viber', static::TTL_VIBER);
$viberMessage->setImage($imageUrl);
$viberMessage->setCaption($textForViberButton);
$viberMessage->setAction($linkForViberButton);
$message->addViber($viberMessage);

$smsMessage = new Hyber\Message\Viber('Text for SMS', static::TTL_SMS);
$message->addSms($smsMessage);

// Now you can send your message. Second parameter is optional,
// it represents when to start message processing
$response = $sender->send($message, new \DateTime('+1 hour'));

// You may receive SuccessResponse...
if ($response instanceof Hyber\Response\SuccessResponse) {
    echo $response->getMessageId();
// ... or ErrorResponse
} elseif ($response instanceof Hyber\Response\ErrorResponse) {
    echo $response->getErrorCode();
    echo $response->getErrorText();
}

// However, if the message was not sent at all -
// you will receive exception from Guzzle(transport layer)
```
