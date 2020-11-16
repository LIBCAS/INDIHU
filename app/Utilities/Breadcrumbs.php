<?php

    namespace WP\Utilities;

    use WP\Entities\WpMenuItem;

     /**
     * @property array $items
     */
    class Breadcrumbs{

        use \Nette\SmartObject;

        /** @var array */
        private $items;
        
        /**
         * @param WpMenuItem $item
         * 
         * @return void
         */
        public function addItem(WpMenuItem $item) : void{
            $this->items[] = $item;
        }

        /**
         * @param array $items
         * 
         * @return void
         */
        public function parse(array $items) : void{
            foreach($items as $title => $link){
                $itemObject = new \stdClass();
                $itemObject->title = $title;
                $itemObject->link = $link;

                $this->items[] = $itemObject;
            }
        }

        /**
         * @return array
         */
        public function getItems() : array{
            return $this->items;
        }
    }
?>