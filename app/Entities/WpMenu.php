<?php
    namespace WP\Entities;

    use WP\Entities\WpMenuItem;
    use WP\Entities\WpPost;

    /**
     * @property int $id
     * @property string $title
     * @property array $items
     */
    class WpMenu{

        use \Nette\SmartObject;

        /** @var int */
        private $id;

        /** @var string */
        private $title;

        /** @var array */
        private $items = [];

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
        public function setId(int $id) : void{
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
         * @return array
         */
        public function getItems() : array{
            return $this->items;
        }

        /**
         * @param array $items
         * @return void
         */
        public function setItems(array $items) : void{
            $this->items = $items;
        }

        /**
         * @param WpMenuItem $item
         * @return void
         */
        public function addItem(WpMenuItem $item) : void{
            $this->items[] = $item;
        }

        /**
         * @return array
         */
        public function jsonSerialize($type = null) : array{
            return [
                'id' => $this->id,
                'title' => $this->title
            ];
        }

        /**
         * @param \stdClass $array
         * @return WpMenu
         */
        public static function map(\stdClass $array) : WpMenu{
            $user = new WpMenu();

            $user->setId($array->term_id);                      
            $user->setTitle($array->name);

            return $user;
        }

    }
?>