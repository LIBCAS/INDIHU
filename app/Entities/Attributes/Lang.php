<?php

namespace WP\Entities\Attributes;

/**
 * @property string $lang
 */
trait Lang{

    /** @var string */
    private $lang = 'cz';

    /**
     * @return string
     */
    public function getLang() : string{
        return $this->lang;
    }

    /**
     * @param string $lang
     * @return void
     */
    public function setLang(string $lang) : void{
        $this->lang = $lang;
    }
}