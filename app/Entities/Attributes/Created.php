<?php

namespace WP\Entities\Attributes;

/**
 * @property string $created
 */
trait Created{

    /** @var string */
    private $created;

    /**
     * @return string
     */
    public function getCreated() : string{
        return $this->created;
    }

    /**
     * @param string $datetime
     * @return void
     */
    public function setCreated(string $datetime) : void{
        $this->created = $datetime;
    }
}