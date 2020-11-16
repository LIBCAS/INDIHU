<?php
    use WP\Utilities\PageRender;
 
    global $post;
    global $container;
    
    $imageId = get_post_meta($post->ID, '_thumbnail_id', true);
    
    if(isset($imageId) && $imageId != ''){
        $fileModel = $container->getByType('WP\Models\FileModel');
        $post->image = $fileModel->getFileById($imageId);
    }else{
        $post->image = null;
    }

    $param = [
        'post' => $post,
    ];
        
    PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/wp/default.latte', $param);
?>