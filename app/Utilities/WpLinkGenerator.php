<?php
    namespace WP\Utilities;

    class WpLinkGenerator{
        
        /**
         * @param string $baseUrl
         * @param string $resource
         * @param string $action
         * @param array $params
         * 
         * @return string
         */
        public static function getLink(string $baseUrl, string $resource, string $action, array $params = []) : string{
            $url = $baseUrl;

            $routes = \WP\Routes::getRoutes();
            $resources = $routes[$resource];
            $url .= '/' . array_search($action, $resources);

            foreach($params as $name => $value){
                if(strpos($url, $name) !== false){
                    $url = str_replace(':' . trim($name), trim($value), $url);
                    unset($params[$name]);
                }
            }

            if(!empty($params)){
                $url .= '/?';
                foreach($params as $name => $value){
                    $url .= $name . '=' . $value . '&';
                }

                $url = rtrim($url, "&");
            }

            return $url;
        }

    }
?>