<?php

namespace Hyber;

use Hyber\Response\ErrorResponse;
use Hyber\Response\SuccessResponse;

class MessageSender
{
    const API_HOST = "https://api.hyber.im";
    const MESSAGE_CREATE_PATH = "/%s/messages/create";
    const CODE_PHONE_NUMBER_INCORRECT = 1154;

    /** @var ApiClient */
    private $apiClient;

    /** @var integer */
    private $identifier;

    /** @var string */
    private $alphaName;

    /** @var string */
    private $callbackUrl;

    /**
     * @param ApiClient $apiClient
     * @param $identifier
     * @param $alphaName
     */
    public function __construct(ApiClient $apiClient, $identifier, $alphaName)
    {
        $this->apiClient = $apiClient;
        $this->identifier = $identifier;
        $this->alphaName = $alphaName;
    }

    /** @param string $callbackUrl */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @param Message $message
     * @param null $startTime
     * @return array|ErrorResponse|SuccessResponse
     * @throws \Exception
     */
    public function send(Message $message, $startTime = null)
    {
        if (null != $startTime) {
            /** @var \DateTime $startTime */
            $startTime = $startTime->format('Y-m-d H:i:s');
        }

        $data = $this->convertMessageToArray($message, $startTime);
        if ($data instanceof ErrorResponse) {
            return $data;
        }

        return $this->doSendMessage($data);
    }

    /**
     * @param array $data
     * @return ErrorResponse|SuccessResponse
     */
    private function doSendMessage(array $data)
    {
        $uri = self::API_HOST . sprintf(self::MESSAGE_CREATE_PATH, $this->identifier);
        $response = $this->apiClient->apiCall($uri, json_encode($data));

        $response = @json_decode($response->getBody(), true);
        if (!is_array($response)) {
            $error = new ErrorResponse();
            $error->setErrorText($response->getBody());

            return $error;
        }

        if (isset($response['error_code']) && isset($response['error_text'])) {
            $error = new ErrorResponse();
            $error->setErrorCode($response['error_code']);
            $error->setErrorText($response['error_text']);

            return $error;
        }

        if (isset($response['message_id'])) {
            return new SuccessResponse($response['message_id']);
        }

        $error = new ErrorResponse();
        $error->setErrorText("Invalid response detected");

        return $error;
    }

    /**
     * @param Message $message
     * @param \DateTime $startTime
     * @return array
     * @throws ErrorResponse
     */
    private function convertMessageToArray(Message $message, $startTime = null)
    {
        $phone = $message->validatePhoneNumber();
        if (null === $phone) {
            $error = new ErrorResponse();
            $error->setErrorCode(self::CODE_PHONE_NUMBER_INCORRECT);
            $error->setErrorText("Invalid phone number: ".$message->getPhoneNumber());

            return $error;
        }

        $channels = $message->convertChannelsToArray();

        $data = [
            'texts' => $channels['texts'],
            'channels' => $channels['channels'],
            'phone_number' => $phone,
            'extra_id' => $message->getExtraId(),
            'callback_url' => $this->callbackUrl,
            'alpha_name' => $this->alphaName
        ];

        if ($tag = $message->getTag()) {
            $data['tag'] = $tag;
        }

        if ($startTime = $message->convertStartTime($startTime)) {
            $data['start_time'] = $startTime;
        }

        if ($channelOptions = $message->buildChannelOptions()) {
            $data['channel_options'] = $channelOptions;
        }

        return $data;
    }
}
