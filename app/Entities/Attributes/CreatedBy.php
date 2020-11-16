<?php

namespace WP\Entities\Attributes;

use Nette\Utils\DateTime;

/**
 * @property id $createdBy
 */
trait CreatedBy{

    /** @var int */
    private $createdBy;

    /**
     * @return integer
     */
    public function getCreatedBy() : int{
        return $this->createdBy;
    }

    /**
     * @param integer $createdBy
     * @return void
     */
    public function setCreatedBy(int $createdBy) : void{
        $this->createdBy = $createdBy;
    }
}