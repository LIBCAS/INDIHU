<?php
    namespace WP\Models;

    use WP\Entities\WpFile;

    class FileModel{

        /**
         * @param integer $fileId
         * @return WpFile|null
         */
        public function getFileById(int $fileId) : ?WpFile{
            global $wpdb;

            $sqlPrepare = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND ID = %d";
            $sql = $wpdb->prepare($sqlPrepare, $fileId);
            $file = $wpdb->get_row($sql);

            if(!$file){
                return null;
            }

            $file->metadata = $this->getFileMetaData($file->ID);

            return WpFile::map($file);
        }

        /**
         * @param integer $fileId
         * @param array $keys
         * @return array
         */
        private function getFileMetaData(int $fileId, array $keys = []) : array{
            global $wpdb;

            $keysEscape = array_map(function($key){
                global $wpdb;
                return $wpdb->prepare("%s", $key);
            }, $keys);
            $keysImplode = implode(',', $keysEscape);

            $sqlPrepare = "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = %d";
            if(!empty($keys)){
                $sqlPrepare .= " AND meta_key IN (". $keysImplode .")";
            }

            $sql = $wpdb->prepare($sqlPrepare, $fileId);
            $metaData = $wpdb->get_results($sql);

            $data = [];
            foreach($metaData as $meta){
                $data[$meta->meta_key] = $meta->meta_value; 
            }

            return $data;
        }
    }
?>