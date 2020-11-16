<?php
    namespace WP\Models;

    use WP\Entities\PostCategory;

    class PostCategoryModel extends WpTaxonomyModel{

        /**
         * @return array
         */
        public function getAllPostCategories() : array{
            $items = $this->getAllWpTaxonomies(PostCategory::TAXONOMY_TYPE);

            return array_map(function($item){
                return PostCategory::map($item);
            }, $items);
        }

         /**
         * @param integer $id
         * @return PostCategory|null
         */
        public function getPostCategoryById(int $id) : ?PostCategory{
            $item = $this->getWpTaxonomyById($id);

            return PostCategory::map($item);
        }
    }
?>