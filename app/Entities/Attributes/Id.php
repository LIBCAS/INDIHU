<?php

namespace WP\Entities\Attributes;

/**
 * @property int $id
 */
trait Id{

    /** @var int */
    private $id;

    /**
     * @return integer
     */
    public function getId() : int{
        return $this->id;
    }

    /**
     * @param integer $id
     * @return void
     */
    public function setId(int $id) : void{
        $this->id = $id;
    }
}