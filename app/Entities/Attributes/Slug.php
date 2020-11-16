<?php

namespace WP\Entities\Attributes;

/**
 * @property string $slug
 */
trait Slug{

    /** @var string */
    private $slug;

    /**
     * @return string
     */
    public function getSlug() : string{
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return void
     */
    public function setSlug(string $slug) : void{
        $this->slug = $slug;
    }
}