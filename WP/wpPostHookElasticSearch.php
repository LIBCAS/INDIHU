<?php
use WP\Services\ElasticSearch;

add_action('wp_insert_post', 'editPost', 10, 3);
function editPost($postId, $post, $update){
    if($update){
        $postType = get_post_type($postId);

        global $container;
        $elasticSearch = $container->getByType('WP\Services\ElasticSearch');

        if($postType == "post"){
            $postModel = $container->getByType('WP\Models\PostModel');
            $post = $postModel->getPostById($postId); 

            if($post){
                $elasticSearch->index($post->toElastic(), $post->id, ElasticSearch::TYPE_POST);
            }
        }

        if($postType == "page"){
            $pageModel = $container->getByType('WP\Models\PageModel');
            $page = $pageModel->getPageById($postId); 

            if($page){
                $elasticSearch->index($page->toElastic(), $page->id, ElasticSearch::TYPE_PAGE);
            }
        }
       
    }
}

add_action('wp_trash_post', 'deletePost', 10, 1);
function deletePost($postId){
    $postType = get_post_type($postId);

    global $container;
    $elasticSearch = $container->getByType('WP\Services\ElasticSearch');

    if($postType == "post"){
        $postModel = $container->getByType('WP\Models\PostModel');
        $post = $postModel->getPostById($postId); 

        if($post){
            $elasticSearch->delete($post->id, ElasticSearch::TYPE_POST);
        }
    }

    if($postType == "page"){
        $pageModel = $container->getByType('WP\Models\PageModel');
        $page = $pageModel->getPageById($postId); 

        if($page){
            $elasticSearch->delete($page->id, ElasticSearch::TYPE_PAGE);
        }
    }
}
?>