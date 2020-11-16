<?php

    namespace WP\Utilities;

    class Validator{
    
        /**
         * @param string $date
         * @return boolean
         */
        public static function validateDate(string $date) : bool{
            return strtotime($date) !== false;
        }
    }
?>