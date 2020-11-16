<?php

namespace WP\Entities\Attributes;

/**
 * @property string $description
 */
trait Description{

    /** @var string */
    private $description;

    /**

     * @return string
     */
    public function getDescription() : string{
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description) : void{
        $this->description = $description;
    }
}