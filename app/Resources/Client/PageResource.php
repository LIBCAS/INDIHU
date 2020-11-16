<?php
    namespace WP\Client\Resources;

    use WP\Models\PageModel;
    use WP\Models\PageSelectModel;

    class PageResource extends Base{

        /** @var PageModel */
        private $pageModel;
        
        /** @var PageSelectModel */
        private $pageSelectModel;

        /**
         * @param PageModel $pageModel
         */
        public function __construct(PageModel $pageModel, PageSelectModel $pageSelectModel){
            $this->pageModel = $pageModel;
            $this->pageSelectModel = $pageSelectModel;
        }

        /**
         * Provides all pages in JSON format
         *
         * @return void
         */
        public function actionApiGetPages(){
            $pages = $this->pageSelectModel->getAllPages();
            $this->sendJson($pages);
        }
    }

?>