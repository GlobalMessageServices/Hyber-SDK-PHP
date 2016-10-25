<?php

namespace Hyber\Message;

class Sms
{
    /** @var string */
    private $text;
    
    /** @var integer */
    private $ttl;

    /**
     * @param string  $text
     * @param integer $ttl
     */
    public function __construct($text, $ttl)
    {
        $this->text = $text;
        $this->ttl = $ttl;
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
}
