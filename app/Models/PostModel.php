<?php
    namespace WP\Models;

    use WP\Entities\Post;
    use WP\Entities\PostCategory;
    use WP\Entities\PostTag;
    use WP\Models\FileModel;

class PostModel extends WpPostModel{

        /** FileModel */
        private $fileModel;

        public function __construct(FileModel $fileModel){
            $this->fileModel = $fileModel;
        }
        
        public function getPostById(int $postId) : ?Post{
            $item = $this->getWpPostById(Post::POST_TYPE, $postId);

            if(!$item){
                return null;
            }

            foreach($item->taxonomies as $name => &$taxonomies){
                foreach($taxonomies as &$taxonomy){
                    switch ($name) {
                        case 'category':
                            $taxonomy = PostCategory::map($taxonomy);
                            break;
                        case 'post_tag':
                            $taxonomy = PostTag::map($taxonomy);
                            break;
                        default:
                            break;
                    }
                }
            }

            if(isset($item->metaData['_thumbnail_id'])){
                $item->image = $this->fileModel->getFileById($item->metaData['_thumbnail_id']);
            }else{
                $item->image = null;
            }

            return Post::map($item);
        }

        public function findPosts(array $filter = [], array $sort = [], int $limit = 0, int $offset = 0) : array{
            $items = $this->findWpPosts($filter, $sort, $limit, $offset);

            foreach($items['items'] as &$item){
                foreach($item->taxonomies as $name => &$taxonomies){
                    foreach($taxonomies as &$taxonomy){
                        switch ($name) {
                            case 'category':
                                $taxonomy->prosecution = $taxonomy->metadata['_category_prosecution'] ? $this->prosecutionModel->getProsecutionById($taxonomy->metadata['_category_prosecution']) : null;
                                $taxonomy = PostCategory::map($taxonomy);
                                break;
                            case 'post_tag':
                                $taxonomy = PostTag::map($taxonomy);
                                break;
                            default:
                                break;
                        }
                    }
                }

                if(isset($item->metaData['_thumbnail_id'])){
                    $item->image = $this->fileModel->getFileById($item->metaData['_thumbnail_id']);
                }else{
                    $item->image = null;
                }

                $item = Post::map($item); 
            }

            return $items;
        }

        private function processFilters($filters){
            global $wpdb;

            $sql = "";
            $sqlData = [];

            $sql .= " AND post_status = 'publish'";

            if(isset($filters['name'])){
                $sql .= " AND post_title LIKE %s";
                $sqlData = "%" . $filters['name'] . "%";
            }

            return $wpdb->prepare($sql, $sqlData);

        }
    }
?>