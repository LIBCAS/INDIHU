<?php

namespace WP\Entities\Attributes;

/**
 * @property string $modified
 */
trait Modified{

    /** @var string */
    private $modified;

    /**
     * @return string
     */
    public function getModified() : ?string{
        return $this->modified;
    }

    /**
     * @param string $datetime
     * @return void
     */
    public function setModified(?string $datetime) : void{
        $this->modified = $datetime;
    }
}