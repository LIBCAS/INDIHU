<?php
    use WP\Utilities\PageRender;
    use WP\Models\PostModel;
    use WP\Models\PostCategoryModel;
    use Nette\Utils\Paginator;

    global $container;
    $postModel = $container->getByType('WP\Models\PostModel');
    $postCategoryModel = $container->getByType('WP\Models\PostCategoryModel');

    $category = get_queried_object();

    $postsCount = 12;

    $args = [
        'type' => 'post',
        'status' => 'publish',
        'tag' => [
            [
                'taxonomy' => 'category',
                'id' => $category->term_id
            ]
        ]
    ];

    $posts = $postModel->findPosts($args, ['created' => 'DESC'], $postsCount, 0);

    $paginator = new Paginator;
    $paginator->setItemCount($posts['count']);
    $paginator->setItemsPerPage($postsCount);
    $paginator->setPage(1); 
    
    $param = [
        'category' => $category,
        'posts' => $posts,
        'showPagination' => true,
        'paginator' => $paginator,
        'count' => $postsCount,
        'categories' => [$category->term_id]
    ];
    
    PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/post/category.latte', $param);
?>