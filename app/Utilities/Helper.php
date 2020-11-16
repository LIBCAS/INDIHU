<?php

    namespace WP\Utilities;

    class Helper{

        /**
         * @param string $content
         * 
         * @return string
         */
        public static function activeShortcode(string $content) : string{
            remove_filter('the_content', 'wpautop');
            return apply_filters('the_content', $content);
        }

        /**
         * @param string $dbTable
         * @param string $name
         * @param integer $id
         * 
         * @return string
         */
        public static function createSlugForName(string $dbTable, string $name, int $id = null) : string{
            $slug = sanitize_title($name);
            
            if(self::checkIfSlugIsUnique($dbTable, $slug, $id)){
                return $slug;
            } else {
                $counter = 1;
                $newSlug = $slug . '-' . $counter;
                
                while(!self::checkIfSlugIsUnique($dbTable, $newSlug, $id)){
                    $counter++;
                    $newSlug = $slug . '-' . $counter;
                }

                return $newSlug;
            }
        }

        /**
         * @param string $dbTable
         * @param string $slug
         * @param integer $id
         * 
         * @return boolean
         */
        private static function checkIfSlugIsUnique(string $dbTable, string $slug, ?int $id) : bool{
            global $wpdb;

            $sqlPrepare = "SELECT count(*) FROM " . $dbTable . " WHERE slug = %s";
            $sqlPrepareData = [$slug];
            if($id){
                $sqlPrepare .= " AND id != %d";
                $sqlPrepareData[] = $id;
            }
            $sql = $wpdb->prepare($sqlPrepare, $sqlPrepareData);
            $slugCount = $wpdb->get_var($sql);

            return $slugCount == 0;
        }
    }
?>