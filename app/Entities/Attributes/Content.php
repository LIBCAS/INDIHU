<?php

namespace WP\Entities\Attributes;

/**
 * @property string $content
 */
trait Content{

    /** @var string */
    private $content;

    /**
     * @return string
     */
    public function getContent() : string{
        return $this->content;
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content) : void{
        $this->content = $content;
    }
}