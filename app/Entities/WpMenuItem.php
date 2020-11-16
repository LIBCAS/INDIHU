<?php
    namespace WP\Entities;

    use WP\Entities\Attributes\Content;


    /**
     * @property int $id
     * @property string $title
     * @property string $link
     * @property string $target
     * @property int $parentId
     * @property array $children
     * @property array $classes
     * @property bool $active
     * @property WpFile $image
     * @property string $position
     * @property string $lang
     */
    class WpMenuItem{

        use \Nette\SmartObject;

        use Content;

        /** @var int */
        private $id;

        /** @var string */
        private $title;

        /** @var string */
        private $link;

        /** @var */
        private $target;

        /** @var int */
        private $parentId;    
        
        /** @var array */
        private $children = [];

        /** @var array */
        private $classes;

        /** @var bool */
        private $active;

        /** @var WpFile */
        private $image;

        /** @var string */
        private $position;

        /** @var string */
        private $lang;

        /**
         * @return integer
         */
        public function getId() : int{
            return $this->id;
        }

        /**
         * @param integer $id
         * @return void
         */
        public function setId(?int $id) : void{
            $this->id = $id;
        }

        /**
         * @return string
         */
        public function getTitle() : string{
            return $this->title;
        }

        /**
         * @param string $title
         * @return void
         */
        public function setTitle(string $title) : void{
            $this->title = $title;
        }

        /**
         * @return string
         */
        public function getLink() : string{
            return $this->link;
        }

        /**
         * @param string $link
         * @return void
         */
        public function setLink(string $link) : void{
            $this->link = $link;
        }

        /**
         * @return string
         */
        public function getTarget() : string{
            return $this->target;
        }

        /**
         * @param string $target
         * @return void
         */
        public function setTarget(string $target) : void{
            $this->target = $target;
        }

        /**
         * @return integer
         */
        public function getParentId() : int{
            return $this->parentId;
        }

        /**
         * @param integer $parentId
         * @return void
         */
        public function setParentId(int $parentId) : void{
            $this->parentId = $parentId;
        }

        /**
         * @param array $children
         * @return void
         */
        public function setChildren(array $children) : void{
            $this->children = $children;
        }

        /**
         * @return array
         */
        public function getChildren() : array{
            return $this->children;
        }

        /**
         * @param WpMenuItem $item
         * @return void
         */
        public function addChild(WpMenuItem $item) : void{
            $this->children[] = $item;
        }

        /**
         * @return array
         */
        public function getClasses() : array{
            return $this->classes;
        }

        /**
         * @param array $classes
         * @return void
         */
        public function setClasses(array $classes) : void{
            $this->classes = $classes;
        }

        /**
         * @return boolean
         */
        public function isActive() : ?bool{
            return $this->active;
        }

        /**
         * @param boolean $active
         * @return void
         */
        public function setActive(bool $active) : void{       
            $this->active = $active;
        }

        /**
         * @return WpFile
         */
        public function getImage() : ?WpFile{
            return $this->image;
        }

        /**
         * @param WpFile $image
         * @return void
         */
        public function setImage(?WpFile $image) : void{
            $this->image = $image;
        }

        /**
         * @return string
         */
        public function getPosition() : string{
            return $this->position;
        }

        /**
         * @param string $position
         * @return void
         */
        public function setPosition(string $position) : void{
            $this->position = $position;
        }

        
        /**
         * @return string
         */
        public function getLang() : string{
            return $this->lang;
        }

        /**
         * @param string $lang
         * @return void
         */
        public function setLang(string $lang) : void{
            $this->lang = $lang;
        }

        /**
         * @return array
         */
        public function jsonSerialize(string $type = "all") : array{
            return [
                'id' => $this->id,
                'title' => $this->title
            ];
        }

        /**
         * @param \stdClass $array
         * @return WpMenuItem
         */
        public static function map(\stdClass $array) : WpMenuItem{ // TODO refactoring
            global $post; 

            $menuItem = new WpMenuItem();

            if($array->metaData['_menu_item_object'] == "category"){
                $menuItem->setId((int)$array->ID);                      
                $menuItem->setTitle($array->post_title != "" ? $array->post_title : $array->info->name);
                $menuItem->setContent($array->post_content);
                $menuItem->setLink(get_category_link($array->info->id));
                $menuItem->setTarget($array->metaData['_menu_item_target']);
                $menuItem->setParentId((int)$array->metaData['_menu_item_menu_item_parent']);
                $menuItem->setClasses(unserialize($array->metaData['_menu_item_classes']));
                if($post){
                    $menuItem->setActive($array->info->id == $post->ID);
                }
                $menuItem->setLang($array->metaData['_menu_item_IQ-lang'] ?? 'cz');

                return $menuItem;
            }

            $menuItem->setId((int)$array->ID);                      
            $menuItem->setTitle($array->post_title != "" ? $array->post_title : $array->info->post_title);
            $menuItem->setContent($array->post_content);
            if($array->metaData['_menu_item_object'] == "custom"){
                $menuItem->setLink($array->metaData['_menu_item_url']);
            }else{
                $menuItem->setLink(get_permalink($array->info->ID));
            }
            $menuItem->setTarget($array->metaData['_menu_item_target']);
            $menuItem->setParentId((int)$array->metaData['_menu_item_menu_item_parent']);
            $menuItem->setClasses(unserialize($array->metaData['_menu_item_classes']));
            if($post){
                $menuItem->setActive($array->info->ID == $post->ID);
            }
            $menuItem->setLang($array->metaData['_menu_item_IQ-lang'] ?? 'cz');

            return $menuItem;
        }
    }

?>