<?php

namespace WP\Entities\Attributes;

/**
 * @property string $name
 */
trait Name{

    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function getName() : string{
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name) : void{
        $this->name = $name;
    }
}