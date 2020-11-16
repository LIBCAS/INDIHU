<?php
    use WP\Utilities\PageRender;
 
    global $post;
    global $container;
 
    $postModel = $container->getByType('WP\Models\PostModel');
    $fileModel = $container->getByType('WP\Models\FileModel');

    if($post->post_type == 'post'){
        $article = $postModel->getPostById($post->ID); // article = Post entity

        $limit = 3;
        $args = [
            'type' => 'post',
            'status' => 'publish',
            'id_not_in' => [ $post->ID ]
        ];
       
        $posts = $postModel->findPosts($args, ['sticky' => true, 'menu_order' => 'ASC'], $limit);
    
        $param = [
            'post' => $article,
            'posts' => $posts
        ];

        PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/post/default.latte', $param);
    } else {        
    
        $imageId = get_post_meta($post->ID, '_thumbnail_id', true);
        
        if(isset($imageId) && $imageId != ''){
            $post->image = $fileModel->getFileById($imageId);
        }else{
            $post->image = null;
        }

        $param = [
            'post' => $post,
            'title' => $post->post_name
        ];
           
        PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/wp/default.latte', $param);
    }
?>