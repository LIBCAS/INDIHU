<?php

namespace WP\Entities\Attributes;

/**
 * @property string $perex
 */
trait Perex{

    /** @var string */
    private $perex;

    /**

     * @return string
     */
    public function getPerex() : string{
        return $this->perex;
    }

    /**
     * @param string $perex
     * @return void
     */
    public function setPerex(string $perex) : void{
        $this->perex = $perex;
    }
}