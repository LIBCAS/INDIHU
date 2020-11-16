<?php
    namespace WP;

    class Routes{
        
        public static function getRoutes(){
            $routes = [
                'Post' => [
                    'api/post' => 'apiFindPost',             
                ],
                'Page' => [
                    'api/pages' => 'apiGetPages'
                ],
                'Menu' => [
                    'api/menus' => 'apiGetMenus'
                ],
                'Location' => [
                    'html/locations' => 'htmlGetLocations'
                ]
            ];

            return $routes;
        }

        /**
         * slug/function name => capabilities
         */
        public static function getAdminPages(){
            $pages = [
               
            ];

            return $pages;
        }

        public static function getApiRoutes(){
            $routes = [
             
            ];

            return $routes;
        }
   
    }
?>