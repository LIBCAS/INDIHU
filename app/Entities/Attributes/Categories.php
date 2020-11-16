<?php

namespace WP\Entities\Attributes;

use WP\Entities\WpTaxonomy;


/**
 * @property array $categories
 */
trait Categories{

    /** @var array */
    private $categories = [];

    /**
     * @return array
     */
    public function getCategories() : array{
        return $this->categories;
    }

    /**
     * @param WpTaxonomy $category
     * @return void
     */
    public function addCategory(WpTaxonomy $category) : void{
        $this->categories[] = $category;
    }

    /**
     * @param array $categories
     * @return void
     */
    public function setCategories(array $categories) : void{
        $this->categories = $categories;
    }
}