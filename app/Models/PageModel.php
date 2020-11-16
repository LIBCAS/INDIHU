<?php
    namespace WP\Models;

    use WP\Entities\Page;
    use Nette\Forms\Form;

    class PageModel extends WpPostModel{

        /** FileModel */
        private $fileModel;

        public function __construct(FileModel $fileModel){
            $this->fileModel = $fileModel;
        }
        
        public function getPageById(int $pageId) : ?Page{
            $item = $this->getWpPostById(Page::POST_TYPE, $pageId);

            if(!$item){
                return null;
            }

            if(isset($item->metaData['_thumbnail_id'])){
                $item->image = $this->fileModel->getFileById($item->metaData['_thumbnail_id']);
            }else{
                $item->image = null;
            }

            return Page::map($item);
        }

        public function findPages(array $filter = [], array $sort = [], int $limit = 0, int $offset = 0) : array{
            $items = $this->findWpPosts($filter, $sort, $limit, $offset);

            foreach($items['items'] as &$item){
                
                if(isset($item->metaData['_thumbnail_id'])){
                    $item->image = $this->fileModel->getFileById($item->metaData['_thumbnail_id']);
                }else{
                    $item->image = null;
                }
    
                $item = Page::map($item);
            }

            return $items;
        }
    }
?>