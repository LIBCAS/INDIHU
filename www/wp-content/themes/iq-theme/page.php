<?php
    use WP\Utilities\PageRender;
    use WP\Entities\WpPost;
    use WP\Entities\WpMenu;
    use WP\Models\PageModel;

    global $params;
    
    if($params){        
        PageRender::renderPage($params['templatePath'], $params);
    }else{

        global $post;
        global $container;

        $pageModel = $container->getByType('WP\Models\PageModel');
        $pageEntity = $pageModel->getPageById($post->ID); // pageEntity = Page entity

        $param = [
            'page' => $pageEntity,
        ]; 

        PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/page/default.latte', $param);
    }

?>