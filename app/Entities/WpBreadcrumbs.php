<?php
    namespace WP\Entities;

    use WP\Entities\WpMenuItem;
    use Nette\Http\UrlScript;
    use Nette\Http\Request;

    /**
     * @property array $items
     */
    class WpBreadcrumbs{

        use \Nette\SmartObject;

        /** @var array */
        private $items;

        public function __construct(){
            $this->addHomePage();
            
            global $post;
            if($post){
                $this->initBreadcrumbs($post);
            }
        }

        /**
         * @return void
         */
        private function addHomePage() : void{
            $menuItem = new WpMenuItem();
            $menuItem->id = null;
            $menuItem->title = get_bloginfo('name');
            $menuItem->link = get_site_url();
            $this->addItem($menuItem);
        }

        /**
         * @param \WP_Post $post
         * @return void
         */
        private function initBreadcrumbs(\WP_Post $post) : void{
            $menuItemsId = array_reverse(get_post_ancestors($post));
            foreach($menuItemsId as $menuItemid){
                $menuItem = new WpMenuItem();
                $menuItem->id = $menuItemid;
                $menuItem->title = get_the_title($menuItemid);
                $menuItem->link = get_permalink($menuItemid);
                $this->addItem($menuItem);
            }

            $menuItem = new WpMenuItem();
            $menuItem->id = $post->ID;
            $menuItem->title = get_the_title($post->id);
            $menuItem->link = get_permalink($post->id);
            $this->addItem($menuItem);
        }

        /**
         * @param WpMenuItem $menuItem
         * @return void
         */
        public function addItem(WpMenuItem $menuItem){
            $this->items[] = $menuItem;
        }

        /**
         * @return array
         */
        public function getItems() : array{
            return $this->items;
        }
    }
?>