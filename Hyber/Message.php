<?php

namespace Hyber;

use Hyber\Message\Push;
use Hyber\Message\Sms;
use Hyber\Message\Viber;
use Hyber\Message\Vk;

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

    /** @var Vk */
    private $vk;
    
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
     * @return Vk
     */
    public function getVk()
    {
        return $this->vk;
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
     * @param Vk $vk
     */
    public function addVk(Vk $vk)
    {
        $this->vk = $vk;
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
            'channel_options' => [],
        ];

        /** @var Push $push */
        if ($push = $this->getPush()) {
            $data['channels'][] = 'push';
            $options = [
                'text' => $push->getText(),
                'ttl' => $push->getTtl(),
            ];

            if ($title = $push->getTitle()) {
                $options['title'] = $title;
            }

            if ($img = $push->getImage()) {
                $options['img'] = $img;
            }

            if ($button = $push->getButton()) {
                $options['caption'] = $button['caption'];
                $options['action'] = $button['link'];
            }

            $data['channel_options']['push'] = $options;
        }

        /** @var Viber $viber */
        if ($viber = $this->getViber()) {
            $data['channels'][] = 'viber';
            $options = [
                'text' => $viber->getText(),
                'ttl' => $viber->getTtl(),
            ];

            if ($img = $viber->getImage()) {
                $options['img'] = $img;
            }

            if ($button = $viber->getButton()) {
                $options['caption'] = $button['caption'];
                $options['action'] = $button['link'];
            }

            if ($iosExpirityText = $viber->getIosExpirityText()) {
                $options['ios_expirity_text'] = $iosExpirityText;
            }

            $data['channel_options']['viber'] = $options;
        }

        /** @var Vk $vk */
        if ($vk = $this->getVk()) {
            $data['channels'][] = 'vk';
            $options = [
                'text' => $vk->getText(),
                'ttl' => $vk->getTtl(),
            ];

            if ($img = $vk->getImage()) {
                $options['img'] = $img;
            }

            if ($button = $vk->getButton()) {
                $options['caption'] = $button['caption'];
                $options['action'] = $button['link'];
            }

            $data['channel_options']['vk'] = $options;
        }

        /** @var Sms $sms */
        $sms = $this->getSms();
        if ($sms) {
            $data['channels'][] = 'sms';
            $data['channel_options']['sms'] = [
                'text' => $sms->getText(),
                'alpha_name' => $sms->getAlphaName(),
                'ttl' => $sms->getTtl(),
            ];
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
}
