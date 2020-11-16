<?php
    namespace WP\Models;

    class BaseModel{

        protected $db;
        
        public function __construct(){
            global $wpdb;
            $this->db = $wpdb;
        }

    }
?>