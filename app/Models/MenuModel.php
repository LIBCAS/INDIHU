<?php
    namespace WP\Models;

    use WP\Entities\WpMenu;
    use WP\Entities\WpMenuItem;


    class MenuModel{

        /** @var PostCategoryModel */
        private $postCategoryModel;

        /** @var FileModel */
        private $fileMode;

        /**
         * @param PostCategoryModel $postCategoryModel
         * @param FileModel $fileModel
         */
        public function __construct(PostCategoryModel $postCategoryModel, FileModel $fileModel){
            $this->postCategoryModel = $postCategoryModel;
            $this->fileModel = $fileModel;
        }

        /**
         * @return array
         */
        public function getAllMenus() : array{
            global $wpdb;

            $sql  = "SELECT t.name, tt.term_taxonomy_id, tt.term_id FROM {$wpdb->prefix}term_taxonomy AS tt";
            $sql .= " LEFT JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id";
            $sql .= " WHERE taxonomy = 'nav_menu'";
            
            $menus = $wpdb->get_results($sql);

            return array_map(function($menu) {
                return WpMenu::map($menu);
            }, $menus);
        }

        /**
         * @param integer $menuId
         * @return WpMenu|null
         */
        public function getMenuById(int $menuId) : ?WpMenu{
            global $wpdb;

            $sqlPrepare = "SELECT * FROM {$wpdb->prefix}terms WHERE term_id = %d";
            $sql = $wpdb->prepare($sqlPrepare, $menuId);
            $menu = $wpdb->get_row($sql);

            if(!$menu){
                return null;
            }

            $menu = WpMenu::map($menu);

            $menuItems = $this->getMenuItems($menu->id);
            foreach($menuItems as $item){
                if($item->parentId === 0){
                    $menu->addItem($item);
                }else{
                    $menuItems[$item->parentId]->addChild($item);
                }

                if ($item->active) {
                    $parentId = $item->parentId;

                    while ($parentId) {
                        if ($menuItems[$parentId]->active) {
                            $parentId = null;
                            continue;
                        }
                        $menuItems[$parentId]->active = true;
                        $parentId = $menuItems[$parentId]->parentId;
                    }
                }
            }

            return $menu;
        }   

        /**
         * @param string $location
         * @return WpMenu|null
         */
        public function getMenuByLocation(string $location) : ?WpMenu {
            $menuLocations = get_nav_menu_locations();
            
            if(isset($menuLocations[$location])){
                return $this->getMenuById($menuLocations[$location]);
            }

            return null;
        }

        /**
         * @param integer $menuId
         * @return array
         */
        private function getMenuItems(int $menuId) : array{
            global $wpdb;

            $sqlPrepare  = "SELECT * FROM {$wpdb->prefix}term_relationships AS tr";
            $sqlPrepare .= " LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID = tr.object_id";
            $sqlPrepare .= " WHERE tr.term_taxonomy_id = %d";
            $sqlPrepare .= " ORDER BY p.menu_order";

            $sql = $wpdb->prepare($sqlPrepare, $menuId);
            $items = $wpdb->get_results($sql);

            $itemsMetaData = $this->getMenuItemsMetaData($items);
            $itemsInfo = $this->getMenuItemsInfo($itemsMetaData);

            $menuItems = [];
            foreach($items as $item){
                $metaData = $itemsMetaData[$item->ID];
                $item->metaData = $metaData;
                if($metaData['_menu_item_object'] == "category"){
                    $item->info = $this->postCategoryModel->getPostCategoryById($metaData['_menu_item_object_id']);
                }else{
                    $item->info = $itemsInfo[$metaData['_menu_item_object_id']];
                    if($metaData['_menu_item_type'] == "post_type_archive"){
                        $item->metaData['link'] = get_post_type_archive_link($metaData['_menu_item_object']); 
                    }
                }
                $menuItems[$item->ID] = WpMenuItem::map($item);
            }
            return $menuItems;
        }

        /**
         * @param array $items
         * @return array
         */
        private function getMenuItemsMetaData(array $items) : array{
            global $wpdb;

            $idsEscape = array_map(function($item){
                global $wpdb;
                return $wpdb->prepare("%d", $item->ID);
            }, $items);
            $idsImplode = implode(',', $idsEscape);

            $sql = "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id IN (". $idsImplode .")";
            $metaData = $wpdb->get_results($sql);

            $data = [];
            foreach($metaData as $meta){
                $data[$meta->post_id][$meta->meta_key] = $meta->meta_value; 
            }

            return $data;
        }

        /**
         * @param array $items
         * @return array
         */
        private function getMenuItemsInfo(array $items) : array{
            global $wpdb;

            $idsEscape = array_map(function($item){
                global $wpdb;
                return $wpdb->prepare("%d", $item['_menu_item_object_id']);
            }, $items);
            $idsImplode = implode(',', $idsEscape);

            $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE ID IN (". $idsImplode .")";
            $info = $wpdb->get_results($sql);

            $data = [];
            foreach($info as $item){
                $data[$item->ID] = $item; 
            }

            return $data;
        }
    }
?>