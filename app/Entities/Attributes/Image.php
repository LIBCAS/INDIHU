<?php

namespace WP\Entities\Attributes;

use WP\Entities\WpFile;

/**
 * @property WpFile $image
 */
trait Image{

    /** @var WpFile */
    private $image = null;

    /**
     * @return WpFile
     */
    public function getImage() : ?WpFile{
        return $this->image;
    }

    /**
     * @param WpFile|null $image
     * @return void
     */
    public function setImage(?WpFile $image)  : void{
        $this->image = $image;
    }
}