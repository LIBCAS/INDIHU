<?php

    namespace WP\Enum;

    abstract class AbstractEnum {

        private $value;

        private function __construct($val){ 
            $this->value = $val; 
        }

        public function __toString(){ 
            return $this->value; 
        }
        
        public static function init(){
            $called_class = get_called_class();
            $reflection = new \ReflectionClass($called_class);
            foreach($reflection->getStaticProperties() as $prop => $dummy){
                $reflection->setStaticPropertyValue($prop, new $called_class($prop));
            }
        }

        // public static function
    }
?>