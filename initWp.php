<?php
use WP\RouteFactory;
use WP\Routes;
use WP\Shortcode;
use WP\GutenbergBlock;
use WP\MetaBox;
use WP\Utilities\ArrayFormat;
use WP\Utilities\RandomStringGenerator;

remove_action('template_redirect', 'redirect_canonical');
remove_filter( 'the_content', 'wpautop' );
remove_filter('the_excerpt', 'wpautop');

// TODO
function inqoolThemeSetup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('align-wide');
    
    register_nav_menu('menuPrimary', 'Hlavní menu');
}
add_action('after_setup_theme', 'inqoolThemeSetup');
// END TODO

function app_output_buffer() {
    ob_start();
}
add_action('init', 'app_output_buffer');

require_once __DIR__ . '/bootstrap.php';

RouteFactory::createRoutes(Routes::getRoutes());

function initApi(){
    RouteFactory::createApiRoutes(Routes::getApiRoutes());
}
add_action('rest_api_init', 'initApi');

function initAdminRoute(){
	RouteFactory::createAdminPages(Routes::getAdminPages());
    RouteFactory::routeAdminAction(Routes::getAdminPages());
    RouteFactory::createMetaBoxes(MetaBox::getMetaBox());
}
add_action('admin_menu', 'initAdminRoute');

RouteFactory::createShortcodes(Shortcode::getShortcode());

function assetPath($manifestPath, $filename) {
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
    } else {
        $manifest = [];
    }

    if (array_key_exists($filename, $manifest)) {
        return $manifest[$filename];
    }

    return $filename;
}

function editorAssets(){
    $serverRoot = $_SERVER['DOCUMENT_ROOT'];

    wp_register_script("iq/posts/scripts",
        get_template_directory_uri() . '/dist/js/' . assetPath($serverRoot . './wp-content/themes/iq-theme/dist/rev-manifest.json', 'gutenberg.js'),
        ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components']
    );
    
    // Styles
	wp_enqueue_style(
        'iq-gutenberg-blocks-css',
        get_template_directory_uri() . '/dist/css/' . assetPath($serverRoot . './wp-content/themes/iq-theme/dist/rev-manifest.json', 'gutenberg.css'),
		['wp-edit-blocks']
	);
}
add_action('enqueue_block_editor_assets', 'editorAssets');

RouteFactory::createGutenbergBlock(GutenbergBlock::getBlock());

// styles and scripts
function enqueueStylesAndScripts() {
    // style
    wp_enqueue_style('css_main', get_template_directory_uri() . '/dist/css/' . assetPath(ABSPATH . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'bundle.css')); 
    
    // script
    wp_enqueue_script('js_main', get_template_directory_uri() . '/dist/js/' . assetPath(ABSPATH . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'bundle.js'), [], "1.0.0", true);
}
add_action('wp_enqueue_scripts', 'enqueueStylesAndScripts');


function enqueueAdminStylesAndScripts() {
    $screen = get_current_screen();

    // style
    wp_enqueue_style('admin_css_font_awesome', get_template_directory_uri() . '/css/font-awesome/font-awesome.min.css'); 
    wp_enqueue_style('admin_css_select2', get_template_directory_uri() . '/css/select2/select2.min.css'); 
    wp_enqueue_style('admin_css_datetimepicker', get_template_directory_uri() . '/css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'); 
    wp_enqueue_style('admin_css_datetimepicker_standalone', get_template_directory_uri() . '/css/bootstrap-datetimepicker/bootstrap-datetimepicker-standalone.css'); 
    wp_enqueue_style('admin_css_main', get_template_directory_uri() . '/dist/css/' . assetPath(ABSPATH . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'admin.css')); 
    
    // script
    wp_enqueue_script('admin_js_select2', get_template_directory_uri() . '/js/select2/select2.full.min.js', [], "1.0.0", true);
    wp_enqueue_script('admin_js_select2_init', get_template_directory_uri() . '/js/select2/select2Init.js', [], "1.0.0", true);
    wp_enqueue_script('admin_js_moment', get_template_directory_uri() . '/js/moment/moment-with-locales.js', [], "1.0.0", true);
    if($screen->id != "toplevel_page_smart-slider3"){
        wp_enqueue_script('admin_js_datetimepicker', get_template_directory_uri() . '/js/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js', [], "1.0.0", true);
        wp_enqueue_script('admin_js_datetimepicker_init', get_template_directory_uri() . '/js/bootstrap-datetimepicker/bootstrap-datetimepickerInit.js', [], "1.0.0", true);
    }
    wp_enqueue_script('admin_js_main', get_template_directory_uri() . '/dist/js/' . assetPath(ABSPATH . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'admin.js'), [], "1.0.0", true);
}
add_action('admin_enqueue_scripts', 'enqueueAdminStylesAndScripts');

// WP magic
function loadAdminMagic(){
    // require_once __DIR__ . '/WP/wpPostHookElasticSearch.php';
}
add_action('admin_init', 'loadAdminMagic');

// Disable emoji
require_once __DIR__ . '/WP/disableEmoji.php';

// widgets
require_once __DIR__ . '/WP/widgets.php';

// Disable XML-RPC
//add_filter( 'xmlrpc_enabled', '__return_false' );

// allow upload file type
function addUploadsMimeTypes($mimes){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'addUploadsMimeTypes');

// add gutenberg category
function gutenbergBlockInQoolCategory($categories, $post){
	$inQoolBlockCategory = [
        'slug' => 'inqool-blocks',
        'title' => 'inQool bloky',
        'icon'  => null,
    ];
    array_unshift($categories, $inQoolBlockCategory);
    return $categories;
}
add_filter('block_categories', 'gutenbergBlockInQoolCategory', 10, 2);

// custom types
require_once __DIR__ . '/WP/customTypes.php';

function set_post_order_in_admin( $wp_query ) {
    if($wp_query->query['post_type'] == 'employee'){
        $wp_query->set( 'orderby', 'title' );
        $wp_query->set( 'order', 'ASC' );
        return;
    } elseif($wp_query->query['post_type'] == 'job_offer') {
        $wp_query->set( 'orderby', 'date' );
        $wp_query->set( 'order', 'DESC' );
        return;
    }    
}
add_filter('pre_get_posts', 'set_post_order_in_admin', 5 );

function show_less_login_info() {
    return '<strong>CHYBA</strong>: Nesprávné přihlašovací údaje!';
}
add_filter( 'login_errors', 'show_less_login_info' );

/* Return status code 404 for existing and non-existing author archives. */
add_action( 'template_redirect',
	function() {
		if ( isset( $_GET['author'] ) || is_author() ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}, 1 );
/* Remove author links. */
add_filter( 'author_link', function() { return '#'; }, 99 );
add_filter( 'the_author_posts_link', '__return_empty_string', 99 );