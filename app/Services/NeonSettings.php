<?php

namespace WP\Services;

use Nette\InvalidArgumentException;

class NeonSettings{
   
    /** @var array */
    private $settings;

    public function __construct(array $settings){
        $this->settings = $settings;
    }

    public function get(string $name){
        if(!$this->exists($name)) {
            throw new InvalidArgumentException("Neon settings does not contain property {$name}");
        }
        return $this->settings[$name];
    }

    public function exists(string $name) : bool{
        return array_key_exists($name, $this->settings);
    }
}