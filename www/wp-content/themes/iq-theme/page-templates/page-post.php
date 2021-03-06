<?php

	/**
	 * Template Name: Příspěvky
	 */
	
    use WP\Utilities\PageRender;
    use WP\Models\PageModel;

	global $post;
	global $container;

	$pageModel = $container->getByType('WP\Models\PageModel');
	$pageEntity = $pageModel->getPageById($post->ID); // pageEntity = Page entity

	$param = [
		'page' => $pageEntity,
		'locations' => $locations
	]; 

	PageRender::renderPage(CLIENT_TEMPLATE_DIR . '/page/post.latte', $param);
?>