<?php

namespace Hyber\Message;

class Sms
{
    /** @var string */
    private $text;
    
    /** @var integer */
    private $ttl;

    /** @var string */
    private $alphaName;

    /**
     * @param string  $text
     * @param integer $ttl
     * @param string  $alphaName
     */
    public function __construct($text, $ttl, $alphaName)
    {
        $this->text = $text;
        $this->ttl = $ttl;
        $this->alphaName = $alphaName;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @return string
     */
    public function getAlphaName()
    {
        return $this->alphaName;
    }
}
