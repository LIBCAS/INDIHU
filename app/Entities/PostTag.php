<?php 
    namespace WP\Entities;

    class PostTag extends WpTaxonomy{
        
        /**
         * @param \stdClass $array
         * @return void
         */
        public static function map(\stdClass $array){
            $postTag = new PostTag();

            $postTag->setId($array->term_id);      
            $postTag->setName($array->name);
            $postTag->setSlug($array->slug);
            
            return $postTag;
        }
    }
?>