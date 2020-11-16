<?php
    namespace WP\Utilities;

    use WP\Utilities\FlashMessage;
    use WP\Entities\WpApp;
    use WP\Entities\WpPost;
    use WP\Entities\WpMenu;
    use WP\Entities\WpBreadcrumbs;
    use WP\Models\MenuModel;

    class PageRender{

        /** @var MenuModal */
        private $menuModel;

        /** @var PartnerModel */
        private $partnerModel;

        /**
         * @param MenuModel $menuModel
         */
        public function __construct(MenuModel $menuModel){
            $this->menuModel = $menuModel; 
        }

        /**
         * @param string $templateName
         * @param array $param
         * 
         * @return void
         */
        public static function renderAdminPage(string $templateName, array $param) : void{
            $serverRoot = $_SERVER['DOCUMENT_ROOT'];
            
            $fm = new FlashMessage(); // TODO
            $param['flashMessages'] = $fm->getMessage();
    
            $param['js_dir'] = get_template_directory_uri() . '/js';
            $param['css_dir'] = get_template_directory_uri() . '/css';
            $param['css_dist_dir'] = get_template_directory_uri() . '/dist/css';
            $param['js_dist_dir'] = get_template_directory_uri() . '/dist/js';
            $param['css_path'] = $param['css_dist_dir'] . "/" . assetPath($serverRoot . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'admin.css');

            render($templateName, $param);
        }

        /**
         * @param string $templateName
         * @param array $param
         * 
         * @return void
         */
        public static function renderPage(string $templateName, array $param) : void{
            global $container;
            $self = $container->getByType('WP\Utilities\PageRender');

            $serverRoot = $_SERVER['DOCUMENT_ROOT'];

            $param['assets_dir'] = get_template_directory_uri() . '/dist';
            $param['js_dir'] = $param['assets_dir'] . '/js';
            $param['css_dir'] = $param['assets_dir'] . '/css';
            $param['img_dir'] = $param['assets_dir'] . '/images';
            $param['css_path'] = $param['css_dir'] . "/" . assetPath($serverRoot . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'bundle.css');
            $param['js_path'] = $param['js_dir'] . "/" . assetPath($serverRoot . '/wp-content/themes/iq-theme/dist/rev-manifest.json', 'bundle.js');
            $param['app'] = new WpApp();
            $param['request'] = $container->getByType('Nette\Http\Request');

            $param['menuPrimary'] = $self->menuModel->getMenuByLocation('menuPrimary');
            $param['menuFooter1'] = $self->menuModel->getMenuByLocation('menuFooter1');
            $param['menuFooter2'] = $self->menuModel->getMenuByLocation('menuFooter2');
            $param['menuFooter3'] = $self->menuModel->getMenuByLocation('menuFooter3');
            $param['menuFooter4'] = $self->menuModel->getMenuByLocation('menuFooter4');

            $param['lang'] = 'cz';

            render($templateName, $param);
        }

        /**
         * @param string $file
         * @param string $templatePath
         * @param string $wpTitle
         * @param array $params
         * 
         * @return void
         */
        public static function loadRender(string $file, string $templatePath, string $wpTitle, array $params) : void{

            add_action('wp_title', function($title) use ( $wpTitle ) { 
                return $wpTitle;
            });

            $params['templatePath'] = $templatePath;

            if($file == "404"){
                \Routes::load($file . '.php', $params, false, 404);
            }else{
                \Routes::load($file . '.php', $params);
            }
        }

        /**
         * @param string $templateName
         * @param array $param
         * 
         * @return string
         */
        public static function renderToString(string $templateName, array $param) : string{
            $param['assets_dir'] = get_template_directory_uri() . '/dist';
            $param['img_dir'] = $param['assets_dir'] . '/images';

            return renderToString($templateName, $param);
        }
    }
?>