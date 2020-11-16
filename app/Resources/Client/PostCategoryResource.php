<?php
    namespace WP\Client\Resources;

    use WP\Models\PostCategoryModel;

    class PostCategoryResource extends Base{

        /** @var PostCategoryModel */
        private $postCategoryModel;

        /**
         * @param PostCategoryModel $postCategoryModel
         */
        public function __construct(PostCategoryModel $postCategoryModel){
            $this->postCategoryModel = $postCategoryModel;
        }
        
        /**
         * @param array $params
         * @return void
         */
        public function actionApiGetPostCategories(array $params) : void{ 
            $categories = $this->postCategoryModel->getAllPostCategories();

            $this->sendJson($categories);        
        }
    }

?>