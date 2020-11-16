<?php
    use WP\Utilities\PageRender;
    use WP\Services\ElasticSearch;

    global $container;

    /** @var ElasticSearch */
    $elasticSearch = $container->getByType('WP\Services\ElasticSearch');

    $itemPerPage = 20;
    $currentPage = ($_GET['pg'] ?? 1);
    $offset = $itemPerPage * $currentPage - $itemPerPage;   

    $currentProsecution = null;
    $prosecution = null;
    if(isset($_GET['prosecution'])){
        $currentProsecution = $_GET['prosecution'];
        $prosecutionModel = $container->getByType('WP\Models\ProsecutionModel');
        $prosecution = $prosecutionModel->getProsecutionById($currentProsecution);
    }

    $currentType = null;
    if(isset($_GET['type'])){
        $currentType = $_GET['type'];
    }

    $results = $elasticSearch->search($_GET, $itemPerPage, $offset);

    if(isset($results['total'])){
        $total = $results['total'];
    }else{
        $total = 0;
    }

    $param = [
        'results' => $results,
        'currentPage' => ($_GET['pg'] ?? 1),
        'currentProsecution' => $currentProsecution,
        'prosecution' => $prosecution,
        'currentType' => $currentType,
        'pagesCount' => (int)ceil($total / $itemPerPage),
        'url' => get_site_url() . '/?s=' . $_GET['s'],
        'itemType' => [
            ElasticSearch::TYPE_POST,
            ElasticSearch::TYPE_PAGE,
            ElasticSearch::TYPE_JOB_OFFER,
            ElasticSearch::TYPE_EMPLOYEE,
        ]
    ];

    

    PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/search/default.latte', $param);

?>