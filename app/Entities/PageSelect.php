<?php
    namespace WP\Entities;

    class PageSelect extends EntitySelect{
        
        /**
         * @param \stdClass $array
         * @return PageSelect
         */
        public static function map(\stdClass $array) : PageSelect{
            $pageSelect = new PageSelect();

            // post type
            $pageSelect->setId($array->ID);
            $pageSelect->setName($array->post_title);

            return $pageSelect;
        }
    }
?>