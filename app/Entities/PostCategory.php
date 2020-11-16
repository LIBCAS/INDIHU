<?php 
    namespace WP\Entities;

    class PostCategory extends WpTaxonomy{

        const TAXONOMY_TYPE = 'category';
        
        /**
         * @param \stdClass $array
         * @return void
         */
        public static function map(\stdClass $array) : PostCategory{
            $postCategory = new PostCategory();

            $postCategory->setId($array->term_id);      
            $postCategory->setName($array->name);
            $postCategory->setSlug($array->slug);
            
            return $postCategory;
        }
    }
?>