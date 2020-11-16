<?php
    namespace WP\Models;

    use WP\Entities\PageSelect;
    use WP\Entities\Page;

    class PageSelectModel{

        public function getAllPages() : array{
            global $wpdb;

            $postType = Page::POST_TYPE;

            $sql = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = '{$postType}' AND post_status = 'publish' ORDER BY post_title";
            $pages = $wpdb->get_results($sql);

            return array_map(function($page){
                return PageSelect::map($page);
            }, $pages);
        }
    }
?>