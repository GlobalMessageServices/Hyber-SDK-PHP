<?php

namespace Hyber\Message;

class Viber
{
    /** @var string */
    private $text;
    
    /** @var integer */
     private $ttl;

    /** @var string */
    private $image;

    /** @var array */
    private $button = [];

    /** @var string */
    private $iosExpirityText;

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

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return array
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * @return string
     */
    public function getIosExpirityText()
    {
        return $this->iosExpirityText;
    }

    /**
     * @param string $image
     */
    public function addImage($image)
    {
        $this->image = $image;
    }

    /**
     * @param string $caption
     * @param string $link
     */
    public function addButton($caption, $link)
    {
        $this->button = [
            'caption' => $caption,
            'link' => $link
        ];
    }

    /**
     * @param string $iosExpirityText
     */
    public function addIosExpirityText($iosExpirityText)
    {
        $this->iosExpirityText = $iosExpirityText;
    }
}
