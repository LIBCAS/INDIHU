<?php
    namespace WP\Models;

    class WpPostModel{

        public function getWpPostById(string $postType, int $postId, array $types = []){
            global $wpdb; 

            if(is_preview() && in_array($postType, ['post', 'page'])){
                if (!current_user_can('edit_post', $postId ) ) {
                    wp_die('Nemáte dostatečné oprávnění pro úpravy tohoto modulu.');
                }
            }
            
            $sqlPrepare = "SELECT * FROM {$wpdb->prefix}posts AS p";
            $sqlPrepare .= " WHERE 1=1";
            
            if(is_preview() && in_array($postType, ['post', 'page'])){
                $sqlPrepare .= " AND p.post_type = 'revision' AND p.post_parent = %d";
            }else{
                $sqlPrepare .= " AND p.post_type = '{$postType}' AND post_status = 'publish'  AND p.ID = %d";
            }
                
            $sqlPrepare .= " ORDER BY p.ID DESC";
            $sqlPrepare .= " LIMIT 1";

            $sql = $wpdb->prepare($sqlPrepare, $postId);
            $item = $wpdb->get_row($sql);

            if(!$item){
                return null;
            }

            // magic WP
            $item->ID = $postId;

            $item->metaData = $this->getWpPostMetaData($item, [], $types)[$postId] ?? [];
            $item->taxonomies = $this->getWpPostTaxonomies($item)[$postId] ?? [];

            return $item;
        }

        /**
         * @param array $filter
         * @param array $sort
         * @param integer $limit
         * @param integer $offset
         * @return array
         */
        public function findWpPosts(array $filter = [], array $sort = [], int $limit = 0, int $offset = 0, array $types = []) : array{
            global $wpdb;

            $sqlPrepare = "";
            $sqlPrepareData = [];
            $selectedIds = [];

            // taxonomy

            // TODO rozdelit jednotlivy tag query

            if(isset($filter['tag']) && is_array($filter['tag']) && count($filter) > 0){
                $sqlPrepare = "SELECT DISTINCT object_id FROM {$wpdb->prefix}term_relationships AS tr";
                $sqlPrepare .= " LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
                $sqlPrepare .= " LEFT JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id";
                $sqlPrepare .= " WHERE 1=1";
                foreach($filter['tag'] as $tag){
                    if(isset($tag['taxonomy'])){
                        $sqlPrepare .= " AND tt.taxonomy = %s"; 
                        $sqlPrepareData[] = $tag['taxonomy'];
                    }
                    if(isset($tag['id'])){
                        $sqlPrepare .= " AND t.term_id = %d";
                        $sqlPrepareData[] = $tag['id'];
                    }
                    if(isset($tag['id_in']) && !empty($tag['id_in'])){
                        $idPrepare = array_map(function($id){
                            global $wpdb;
                            return $wpdb->prepare("%d", $id);
                        }, $tag['id_in']);
                        $idImplode = implode(',', $idPrepare);
                        $sqlPrepare .= " AND t.term_id IN ({$idImplode})";
                    }
                    if(isset($tag['slug'])){
                        $sqlPrepare .= " AND t.slug = %s";
                        $sqlPrepareData[] = $tag['slug'];
                    }
                    if(isset($tag['slug_in'])){
                        $slugPrepare = array_map(function($slug){
                            global $wpdb;
                            return $wpdb->prepare("%s", $slug);
                        }, $tag['slug_in']);
                        $slugImplode = implode(',', $slugPrepare);
                        $sqlPrepare .= " AND t.slug IN ({$slugImplode})";
                    }
                }

                if(!empty($sqlPrepareData)){
                    $sql = $wpdb->prepare($sqlPrepare, $sqlPrepareData);
                }else{
                    $sql = $sqlPrepare;
                }

                $selectedIds = array_map(function($item){
                    return $item->object_id;
                }, $wpdb->get_results($sql));
            }

            // TODO filtrovani podle post_id a post_id_in vzít jen ty id ktere jsou v obou polích

            $sqlPrepare = "";
            $sqlPrepareData = [];

            $sqlPrepare  .=  "SELECT p.*";

            $metaCount = 1;
            if(isset($filter['meta']) && is_array($filter['meta'])){
                foreach($filter['meta'] as $meta){
                    $sqlPrepare .= ", pm{$metaCount}.meta_value `{$meta['key']}`";
                    $metaCount++;
                }
            }

            $sqlPrepare .= " FROM {$wpdb->prefix}posts AS p";

            $metaCount = 1;
            if(isset($filter['meta']) && is_array($filter['meta'])){
                foreach($filter['meta'] as $meta){
                    $sqlPrepare .= " INNER JOIN {$wpdb->prefix}postmeta AS pm{$metaCount} ON pm{$metaCount}.post_id = p.ID";
                    $metaCount++;
                }
            }

            $sqlPrepare .= " WHERE 1=1";

            $metaCount = 1;
            if(isset($filter['meta']) && is_array($filter['meta'])){
                foreach($filter['meta'] as $meta){
                    if($meta['compare'] === null){
                        $sqlPrepare .= " AND (pm{$metaCount}.meta_key = %s)";
                        $sqlPrepareData[] = $meta['key'];
                    }else{
                        $sqlPrepare .= " AND (pm{$metaCount}.meta_key = %s AND pm{$metaCount}.meta_value {$meta['compare']} %s)";
                        $sqlPrepareData[] = $meta['key'];
                        $sqlPrepareData[] = $meta['value'];
                    }
                    $metaCount++;
                }
            }

            if(isset($filter['id_in']) && !empty($filter['id_in'])){
                $selectedIds = array_merge($selectedIds, $filter['id_in']);
            }

            if(!empty($selectedIds) || isset($filter['tag'])){
                $selectedIdsImplode = implode(',', $selectedIds);
                $sqlPrepare .= " AND p.id IN ({$selectedIdsImplode})";
            }

            if(isset($filter['id_not_in']) && !empty($filter['id_not_in'])){
                $selectedIdsImplode = implode(',', $filter['id_not_in']);
                $sqlPrepare .= " AND p.id NOT IN ({$selectedIdsImplode})";
            }

            if(isset($filter['type'])){
                $sqlPrepare .= " AND p.post_type = %s";
                $sqlPrepareData[] = $filter['type'];
            }

            if(isset($filter['parent'])){
                $sqlPrepare .= " AND p.post_parent = %s";
                $sqlPrepareData[] = $filter['parent'];
            }

            if(isset($filter['year'])){
                $sqlPrepare .= " AND YEAR(p.post_date) = %d";
                $sqlPrepareData[] = $filter['year'];
            }

            if(isset($filter['status'])){
                $sqlPrepare .= " AND p.post_status = %s";
                $sqlPrepareData[] = $filter['status'];
            }

            if(isset($filter['name'])){
                $sqlPrepare .= " AND p.post_title = %s";
                $sqlPrepareData[] = $filter['name']; 
            }
            
            if(isset($filter['name_like'])){
                $sqlPrepare .= " AND p.post_title LIKE %s";
                $sqlPrepareData[] = '%' . $filter['name_like'] . '%'; 
            }

            if(isset($filter['status_in'])){
                $statusPrepare = array_map(function($status){
                    global $wpdb;
                    return $wpdb->prepare("%s", $status);
                }, $filter['status_in']);
                $statusImplode = implode(',', $statusPrepare);
                $sqlPrepare .= " AND p.post_status IN ({$statusImplode})";
            }

            if(empty($sqlPrepareData)){
                $sql = $sqlPrepare;
            }else{
                $sql = $wpdb->prepare($sqlPrepare, $sqlPrepareData);
            }
            $itemsCount = count($wpdb->get_results($sql));

            if(!empty($sort)){
                $sqlPrepare .= " ORDER BY";
                $first = true;
                foreach($sort as $name => $direction){

                    if(!$first){
                        $sqlPrepare .= " ,";
                    }

                    if($name == "rand"){
                        $sqlPrepare .= " RAND()";
                    }elseif($name == "sticky"){
                        $sticky = get_option('sticky_posts');
                        if(empty($sticky)){
                            continue;
                        }
                        $idPrepare = array_map(function($id){
                            global $wpdb;
                            return $wpdb->prepare("%d", $id);
                        }, $sticky);
                        $idsImplode = implode(',', $idPrepare);
                        $sqlPrepare .= " FIELD(ID, {$idsImplode}) DESC";
                    }else{
                        $allowName = [
                            'id',
                            'post_title',
                            'post_date',
                            'menu_order'
                        ];

                        $allowDirection = [
                            'asc',
                            'desc'
                        ];
                        
                        $sqlPrepare .= " " . (in_array(strtolower($name), $allowName) ? '`'.$name.'`' : "id");
                        $sqlPrepare .= " " . (in_array(strtolower($direction), $allowDirection) ? $direction : "desc");
                    }

                    $first = false;
                }
            }
            if($limit > 0){
                $sqlPrepare .= " LIMIT %d";
                $sqlPrepareData[] = $limit + 1; 
                $sqlPrepare .= " OFFSET %d";
                $sqlPrepareData[] = $offset;
            }

            if(empty($sqlPrepareData)){
                $sql = $sqlPrepare;
            }else{
                $sql = $wpdb->prepare($sqlPrepare, $sqlPrepareData);
            }

            $items = $wpdb->get_results($sql);

            $hasNext = false;
            if($limit > 0){
                if(count($items) == $limit + 1){
                    $hasNext = true;
                    array_pop($items);
                }
            }

            $metaData = $this->getWpPostsMetaData($items, [], $types);
            $taxonomies = $this->getWpPostsTaxonomies($items);
            
            $itemsObject = array_map(function($item) use ($metaData, $taxonomies){
                $item->metaData = $metaData[$item->ID] ?? [];
                $item->taxonomies = $taxonomies[$item->ID] ?? [];
                return $item;
            }, $items);

            $response = [
                'items' => $itemsObject,
                'count' => $itemsCount,
                'pages' => $limit > 0 ? (int)ceil($itemsCount / $limit) : 0,
                'hasNext' => $hasNext
            ];

            return $response;
        }

        private function getWpPostMetaData($post, array $keys = [], array $types = []) : array{
            return $this->getWpPostsMetaData([$post], $keys, $types);
        } 

        /**
         * @param array $posts
         * @param array $keys
         * @return array
         */
        private function getWpPostsMetaData(array $posts, array $keys = [], array $types = []) : array{
            global $wpdb;

            $idsEscape = array_map(function($post){
                global $wpdb;
                return $wpdb->prepare("%d", $post->ID);
            }, $posts);
            $idsImplode = implode(',', $idsEscape);

            $keysEscape = array_map(function($key){
                global $wpdb;
                return $wpdb->prepare("%s", $key);
            }, $keys);
            $keysImplode = implode(',', $keysEscape);

            $sql = "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id IN (". $idsImplode .")";
            if(!empty($keys)){
                $sql .= " AND meta_key IN (". $keysImplode .")";
            }

            $metaData = $wpdb->get_results($sql);

            $data = [];
            $typesKeys = array_keys($types);
            foreach($metaData as $meta){

                if(in_array($meta->meta_key, $typesKeys)){
                    switch ($types[$meta->meta_key]) {
                        case 'array':
                            $data[$meta->post_id][$meta->meta_key][] = $meta->meta_value;
                            break;
                        default:
                            $data[$meta->post_id][$meta->meta_key] = $meta->meta_value;
                            break;
                    }
                }else{
                    $data[$meta->post_id][$meta->meta_key] = $meta->meta_value; 
                }
            }

            return $data;
        }

        private function getWpPostTaxonomies($post) : array{
            return $this->getWpPostsTaxonomies([$post]);
        }

        private function getWpPostsTaxonomies(array $posts) : array{
            global $wpdb;

            $idsEscape = array_map(function($post){
                global $wpdb;
                return $wpdb->prepare("%d", $post->ID);
            }, $posts);
            $idsImplode = implode(',', $idsEscape);

            $sql  = "SELECT * FROM {$wpdb->prefix}term_relationships AS tr";
            $sql .= " LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
            $sql .= " LEFT JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id";
            $sql .= " WHERE object_id IN (" . $idsImplode . ")";

            $taxonomies = $wpdb->get_results($sql);

            $taxonomiesMeta = [];
            if(!empty($taxonomies)){
                $taxonomiesMeta = $this->getTaxonomiesMetadata($taxonomies);
            }

            $data = [];
            foreach($taxonomies as $taxonomy){
                if($taxonomiesMeta){
                    if(isset($taxonomiesMeta[$taxonomy->term_id])){
                        $taxonomy->metadata = $taxonomiesMeta[$taxonomy->term_id];
                    }
                }
                $data[$taxonomy->object_id][$taxonomy->taxonomy][] = $taxonomy; 
            }

            return $data;
        }

        private function getTaxonomiesMetadata(array $taxonomies, array $keys = []) : array{
            global $wpdb;

            $idsEscape = array_map(function($category){
                global $wpdb;
                return $wpdb->prepare("%d", $category->term_id);
            }, $taxonomies);
            $idsImplode = implode(',', $idsEscape);

            $keysEscape = array_map(function($key){
                global $wpdb;
                return $wpdb->prepare("%s", $key);
            }, $keys);
            $keysImplode = implode(',', $keysEscape);

            $sql = "SELECT * FROM {$wpdb->prefix}termmeta WHERE term_id IN (". $idsImplode .")";
            if(!empty($keys)){
                $sql .= " AND meta_key IN (". $keysImplode .")";
            }

            $metaData = $wpdb->get_results($sql);

            $data = [];
            foreach($metaData as $meta){
                $data[$meta->term_id][$meta->meta_key] = $meta->meta_value; 
            }

            return $data;
        }
    }
?>