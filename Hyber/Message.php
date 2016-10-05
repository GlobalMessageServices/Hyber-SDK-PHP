<?php

namespace Hyber;

use Hyber\Message\Push;
use Hyber\Message\Sms;
use Hyber\Message\Viber;

class Message
{
    /** @var array */
    private $symbolsToIgnore = ['+', '(', ')', '-', ' '];

    /** @var string */
    private $phoneNumber;

    /** @var integer */
    private $extraId;

    /** @var string */
    private $tag;

    /** @var boolean */
    private $isPromotional;
    
    /** @var Push */
    private $push;
    
    /** @var Viber */
    private $viber;
    
    /** @var Sms */
    private $sms;

    /**
     * @param string $phoneNumber
     */
    public function __construct($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return int
     */
    public function getExtraId()
    {
        return $this->extraId;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return boolean
     */
    public function getIsPromotional()
    {
        return $this->isPromotional;
    }

    /**
     * @return Push
     */
    public function getPush()
    {
        return $this->push;
    }

    /**
     * @return Viber
     */
    public function getViber()
    {
        return $this->viber;
    }

    /**
     * @return Sms
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * @param Push $push
     */
    public function addPush(Push $push)
    {
        $this->push = $push;
    }

    /**
     * @param Viber $viber
     */
    public function addViber(Viber $viber)
    {
        $this->viber = $viber;
    }

    /**
     * @param Sms $sms
     */
    public function addSms(Sms $sms)
    {
        $this->sms = $sms;
    }

    /**
     * @param int $extraId
     */
    public function setExtraId($extraId)
    {
        $this->extraId = $extraId;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param boolean $isPromotional
     */
    public function setIsPromotional($isPromotional)
    {
        $this->isPromotional = $isPromotional;
    }

    /**
     * @return string|null
     */
    public function validatePhoneNumber()
    {
        $phone = str_replace($this->symbolsToIgnore, "", trim($this->getPhoneNumber()));
        if (false === is_numeric($phone)) {
            return null;
        }

        return $phone;
    }

    /**
     * @return array
     */
    public function convertChannelsToArray()
    {
        $data = [
            'channels' => [],
            'texts' => [],
        ];

        /** @var Push $push */
        $push = $this->getPush();
        if ($push) {
            $data['channels'][] = [
                'channel' => 'push',
                'ttl' => $push->getTtl(),
            ];

            $data['texts'][] = $push->getText();
        }

        /** @var Viber $viber */
        $viber = $this->getViber();
        if ($viber) {
            $data['channels'][] = [
                'channel' => 'viber',
                'ttl' => $viber->getTtl(),
            ];

            $data['texts'][] = $viber->getText();
        }

        /** @var Sms $sms */
        $sms = $this->getSms();
        if ($sms) {
            $data['channels'][] = [
                'channel' => 'sms',
                'ttl' => $sms->getTtl(),
            ];

            $data['texts'][] = $sms->getText();
        }

        return $data;
    }

    /**
     * @param $dateTime
     * @return null|string
     */
    public function convertStartTime($dateTime)
    {
        $dateTime = date("Y-m-d H:i:s", strtotime($dateTime));
        if ($dateTime <= date("Y-m-d H:i:s")) {
            return null;
        } else {
            return $dateTime;
        }
    }

    /**
     * @return array
     */
    public function buildChannelOptions()
    {
        $channelOptions = [];

        /** @var Push $push */
        $push = $this->getPush();
        if ($push) {
            $img = $push->getImage();
            if ($img) {
                $channelOptions['push']['img'] = $img;
            }

            $button = $push->getButton();
            if ($button) {
                $channelOptions['push']['caption'] = $button['caption'];
                $channelOptions['push']['action'] = $button['link'];
            }
        }

        /** @var Viber $viber */
        $viber = $this->getViber();
        if ($viber) {
            $img = $viber->getImage();
            if ($img) {
                $channelOptions['viber']['img'] = $img;
            }

            $button = $viber->getButton();
            if ($button) {
                $channelOptions['viber']['caption'] = $button['caption'];
                $channelOptions['viber']['action'] = $button['link'];
            }

            $iosExpirityText = $viber->getIosExpirityText();
            if ($iosExpirityText) {
                $channelOptions['viber']['ios_expirity_text'] = $iosExpirityText;
            }
        }

        return $channelOptions;
    }
}
