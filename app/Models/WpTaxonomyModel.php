<?php
    namespace WP\Models;

    class WpTaxonomyModel{

        public function getAllWpTaxonomies(string $taxonomyType){
            global $wpdb;

            $sql = "SELECT * FROM {$wpdb->prefix}term_taxonomy AS tt";
            $sql .= " LEFT JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id";
            $sql .= " WHERE tt.taxonomy = '{$taxonomyType}'";

            $items = $wpdb->get_results($sql);
            return $items;
        }

        public function getWpTaxonomyById(int $id){
            global $wpdb;

            $sqlPrepare  = "SELECT * FROM {$wpdb->prefix}terms";
            $sqlPrepare .= " WHERE term_id = %d";

            $sql = $wpdb->prepare($sqlPrepare, $id);
            $taxonomy = $wpdb->get_row($sql);

            $taxonomy->metaData = $this->getTaxonomyMetaData($taxonomy);
            
            return $taxonomy;
        }

        private function getTaxonomyMetaData($taxonomy) : array{
            return $this->getTaxonomiesMetadata([$taxonomy])[$taxonomy->term_id] ?? [];
        }

        private function getTaxonomiesMetadata(array $taxonomies, array $keys = []) : array{
            global $wpdb;

            $idsEscape = array_map(function($category){
                global $wpdb;
                return $wpdb->prepare("%d", $category->term_id);
            }, $taxonomies);
            $idsImplode = implode(',', $idsEscape);

            $keysEscape = array_map(function($key){
                global $wpdb;
                return $wpdb->prepare("%s", $key);
            }, $keys);
            $keysImplode = implode(',', $keysEscape);

            $sql = "SELECT * FROM {$wpdb->prefix}termmeta WHERE term_id IN (". $idsImplode .")";
            if(!empty($keys)){
                $sql .= " AND meta_key IN (". $keysImplode .")";
            }

            $metaData = $wpdb->get_results($sql);

            $data = [];
            foreach($metaData as $meta){
                $data[$meta->term_id][$meta->meta_key] = $meta->meta_value; 
            }

            return $data;
        }

       
    }
?>