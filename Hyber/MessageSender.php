<?php

namespace Hyber;

use Http\Client\Exception\HttpException;
use Hyber\Response\ErrorResponse;
use Hyber\Response\SuccessResponse;

class MessageSender
{
    const DEFAULT_API_URL = "https://api-v2.hyber.im/%s";
    const CODE_PHONE_NUMBER_INCORRECT = 1154;

    /** @var ApiClient */
    private $apiClient;

    /** @var integer */
    private $identifier;

    /** @var string */
    private $callbackUrl;

    /** @var string */
    private $apiUrl = self::DEFAULT_API_URL;

    /**
     * @param ApiClient $apiClient
     * @param integer   $identifier
     */
    public function __construct(ApiClient $apiClient, $identifier)
    {
        $this->apiClient = $apiClient;
        $this->identifier = $identifier;
    }

    /** @param string $callbackUrl */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
    }

    /** @param string $apiUrl */
    public function overrideApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
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
        try {
            $response = $this->apiClient->apiCall(sprintf($this->apiUrl, $this->identifier), json_encode($data));
            $response = @json_decode($response->getBody(), true);

            if (isset($response['message_id'])) {
                return new SuccessResponse($response['message_id']);
            }

            $error = new ErrorResponse();
            $error->setErrorText("Invalid response detected");
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                $response = @json_decode($e->getResponse()->getBody(), true);

                if (isset($response['error_code']) && isset($response['error_text'])) {
                    $error = new ErrorResponse();
                    $error->setHttpCode($e->getCode());
                    $error->setErrorCode($response['error_code']);
                    $error->setErrorText($response['error_text']);

                    return $error;
                }
            }

            $error = new ErrorResponse();
            $error->setHttpCode($e->getCode());
            $error->setErrorText($e->getMessage());

            return $error;
        }
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
            'channels' => $channels['channels'],
            'channel_options' => $channels['channel_options'],
        ];
        
        $data['phone_number'] = $phone;

        if ($extraId = $message->getExtraId()) {
            $data['extra_id'] = $extraId;
        }

        if ($callbackUrl = $this->callbackUrl) {
            $data['callback_url'] = $callbackUrl;
        }

        if ($startTime = $message->convertStartTime($startTime)) {
            $data['start_time'] = $startTime;
        }

        if ($tag = $message->getTag()) {
            $data['tag'] = $tag;
        }

        if ($isPromotional = $message->getIsPromotional()) {
            $data['is_promotional'] = $isPromotional;
        }

        return $data;
    }
}
