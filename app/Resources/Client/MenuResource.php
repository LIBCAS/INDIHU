<?php
    namespace WP\Client\Resources;

    use WP\Models\MenuModel;

    class MenuResource extends Base{

        /** @var MenuModel */
        private $menuModel;

        /**
         * @param MenuModel $menuModel
         */
        public function __construct(MenuModel $menuModel){
            $this->menuModel = $menuModel;
        }

        /**
         * @return void
         */
        public function actionApiGetMenus() : void{                        
            $menus = $this->menuModel->getAllMenus();

            $this->sendJson($menus);
        }

        /**
         * @param array $params
         * @return string
         */
        public function blockGuidepost(array $params) : string{

            $param = [
                'className' => $params['className'],
                'menu' => $this->menuModel->getMenuById($params['selectedMenuId'])
            ];

            return $this->renderShortcode(CLIENT_TEMPLATE_DIR . '/menu/blockGuidepost.latte', $param);
        }
    }

?>