<?php

namespace Hyber\Response;

class SuccessResponse
{
    /** @var int */
    private $messageId;

    /**
     * @param $messageId
     */
    public function __construct($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return $this->messageId;
    }
}
