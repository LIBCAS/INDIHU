<?php
    namespace WP;

    use WP\Routes;

    class RouteFactory{

        const API_BASE_URL = 'pnp';
        const API_VERSION = 'v1';

        public static function createRoutes($routes){
            foreach($routes as $resources => $route){
                foreach($route as $patter => $func){
                    \Routes::map($patter, function($params) use ($resources, $func){
                        $className = "\\WP\\Client\\Resources\\{$resources}Resource";
                        $functionName = "action{$func}";
                        
                        global $container;
                        $classObject = $container->getByType($className);
                        // $classObject->checkPermissions($functionName);
                        $classObject->$functionName($params);
                    });
                }
            }
        }

        public static function createAdminPages($pages){
            foreach($pages as $resource => $page){

                $pageSlug = $resource;

                if(isset($page['customType']) && $page['customType']){
                    $pageSlug = $page['slug'];
                }else{
                    \add_menu_page(
                        $page['title'], 
                        $page['title'], 
                        $page['capp'], 
                        $pageSlug, 
                        function() use ($resource){
                            $className = "\\WP\\Admin\\Resources\\{$resource}Resource";
                            $functionName = "action{$resource}";
                            
                            global $container;
                            $classObject = $container->getByType($className);
                            $classObject->$functionName();
                        }, 
                        $page['icon'], 
                        $page['position']
                    );
                }

                foreach($page['pages'] as $subPageSlug => $subPage){
                    \add_submenu_page(
                        $subPage['show'] ? $pageSlug : null,
                        $subPage['title'], 
                        $subPage['title'], 
                        $subPage['capp'], 
                        $subPageSlug, 
                        function() use ($resource, $subPageSlug){
                            $className = "\\WP\\Admin\\Resources\\{$resource}Resource";
                            $functionName = "action{$subPageSlug}";
                            
                            global $container;
                            $classObject = $container->getByType($className);
                            $classObject->$functionName();
                        }
                    );
                }
            }
        }

        public static function routeAdminAction($routes){
            if(isset($_GET['act'])){
                $page = null;
                if(isset($routes[$_GET['page']])){
                    $page = $_GET['page'];
                    $func = $routes[$_GET['page']]['action'][$_GET['act']];
                }else{
                    foreach($routes as $resourceName => $pages){
                        foreach($pages['pages'] as $pageName => $pageData){
                            if($pageName == $_GET['page']){
                                $page = $resourceName;
                                $func = $pageData['action'][$_GET['act']];
                            }
                        }
                    }
                }

                $className = "\\WP\\Admin\\Resources\\{$page}Resource";
                $functionName = "action{$func}";

                global $container;
                $classObject = $container->getByType($className);
                $classObject->$functionName();
            }
        }

        public static function createShortcodes($shortcodes){
            foreach($shortcodes as $resource => $shortcode){
                foreach($shortcode as $name => $func){
                    add_shortcode(
                        $name,
                        function() use ($resource, $func){
                            $className = "\\WP\\Client\\Resources\\{$resource}Resource";
                            $functionName = "shortcode{$func}";

                            $args = func_get_args();
                            
                            global $container;
                            $classObject = $container->getByType($className);
                            return $classObject->$functionName(...$args);
                        }
                    );
                }
            }
        }
        
        public static function createGutenbergBlock($blocks){

            $attributeDefailtValue = [
                'integer' => 0,
                'string' => "",
                'array' => [],
                'boolean' => true
            ];

            foreach($blocks as $resource => $block){
                foreach($block as $name => $data){

                    // callback
                    $callback = $name;
                    if(isset($data['callback'])){
                        $callback = $data['callback'];
                    }

                    // attributes
                    $prepareAttributes = [
                        'className' => [
                            'type' => 'string',
                            'default' => ''
                        ]
                    ];
                    foreach($data['attributes'] as $attributeName => $attribute){
                        if(is_array($attribute)){
                            $prepareAttributes[$attributeName] = $attribute;
                        }else{
                            $prepareAttributes[$attributeName] = [
                                'type' => $attribute,
                                'default' => $attributeDefailtValue[$attribute]
                            ];
                        }
                    }

                    register_block_type(
                        'iq/' . $name, [
                            'editor_script' => 'iq/posts/scripts',
                            'render_callback' => function() use ($resource, $callback){
                                $className = "\\WP\\Client\\Resources\\{$resource}Resource";
                                $functionName = "block{$callback}";

                                $args = func_get_args();

                                global $container;
                                $classObject = $container->getByType($className);
                                return $classObject->$functionName(...$args);
                            },
                            'attributes' => $prepareAttributes
                        ]
                    );
                }
            }
        }

        public static function createMetaBoxes($metaBoxes){
            foreach($metaBoxes as $resource => $box){
                foreach($box as $boxId => $boxData){
                    \add_meta_box(
                        $boxId, 
                        $boxData['title'],
                        function() use ($resource, $boxData){
                            $className = "\\WP\\Admin\\Resources\\{$resource}Resource";
                            $functionName = "metaBox{$boxData['callback']}";

                            $args = func_get_args();

                            global $container;
                            $classObject = $container->getByType($className);
                            $classObject->$functionName(...$args);
                        },
                        $boxData['screen'],
                        $boxData['position'],
                        $boxData['priority']
                    );

                    if(array_key_exists("save", $boxData) && $boxData['save'] === null){
                        continue;
                    }
                    
                    add_action(
                        "save_post_{$boxData['screen']}", 
                        function() use ($resource, $boxData){
                            $className = "\\WP\\Admin\\Resources\\{$resource}Resource";
                            if(isset($boxData['save'])){
                                $functionName = "saveMetaBox{$boxData['save']}";
                            }else{
                                $functionName = "saveMetaBox{$boxData['callback']}";
                            }

                            $args = func_get_args();

                            global $container;
                            $classObject = $container->getByType($className);
                            $classObject->$functionName(...$args);
                        },
                        10,
                        3
                    );
                }
            }
        }
        
        public static function createApiRoutes($resourcesRoutes){

            foreach($resourcesRoutes as $resource => $methodRoutes){
                foreach($methodRoutes as $method => $routes){
                    foreach($routes as $route){
                        register_rest_route(self::API_BASE_URL . '/' . self::API_VERSION, $route['url'], [
                            [
                                'methods' => $method,
                                'callback' => function() use ($resource, $route){
                                    $className = "\\WP\\Client\\Resources\\{$resource}Resource";
                                    $functionName = "api{$route['callback']}";
    
                                    $args = func_get_args();
    
                                    global $container;
                                    $classObject = $container->getByType($className);
                                    return $classObject->$functionName(...$args);
                                },
                            ]
                        ] );
                    }
                }
            }


        }

    }
?>