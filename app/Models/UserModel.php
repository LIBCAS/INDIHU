<?php
    namespace WP\Models;

    use WP\Entities\WpUser;

    class UserModel{

        /**
         * @return WpUser|null
         */
        public function getCurrentUser() : ?WpUser{
            $currentUserId =  get_current_user_id();
            $user = $this->getUserById($currentUserId);
            
            return $user;
        }

        /**
         * @param integer $userId
         * @return WpUser|null
         */
        public function getUserById(int $userId) : ?WpUser{
            global $wpdb;

            $sqlPrepare = "SELECT * FROM {$wpdb->prefix}users WHERE id = %d";
            $sql = $wpdb->prepare($sqlPrepare, $userId);
            $user = $wpdb->get_row($sql);

            if(!$user){
                return null; 
            } 

            $sqlPrepare = "SELECT * FROM {$wpdb->prefix}usermeta WHERE user_id = %d";
            $sql = $wpdb->prepare($sqlPrepare, $userId);
            $userMetaData = $wpdb->get_results($sql);
            
            $userMeta = [];
            foreach($userMetaData as $data){
                $userMeta[$data->meta_key] = $data->meta_value;
            }

            $user->firstName = $userMeta['first_name'] ?? null;
            $user->lastName = $userMeta['last_name'] ?? null;
            $user->roles = isset($userMeta["{$wpdb->prefix}capabilities"]) ? array_keys(unserialize($userMeta["{$wpdb->prefix}capabilities"])) : ['guest'];

            return WpUser::map($user);
        }   

        public function findUsers(array $filter = []){
            global $wpdb;

            $sqlPrepareData = [];
            $sqlPrepare  = "SELECT u.* FROM {$wpdb->prefix}users AS u";
            if(!empty($filter)){
                $sqlPrepare .= " INNER JOIN {$wpdb->prefix}usermeta AS um ON u.ID = um.user_id";
            }
            $sqlPrepare .= " WHERE 1=1";

            if(isset($filter['role'])){
                $sqlPrepare .= " AND (um.meta_key = '{$wpdb->prefix}capabilities' AND um.meta_value LIKE %s)";
                $sqlPrepareData = '%"' . $filter['role'] . '"%'; 
            }

            $sqlPrepare .= " GROUP BY u.ID"; 

            if(!empty($sqlPrepareData)){
                $sql = $wpdb->prepare($sqlPrepare, $sqlPrepareData);
            }else{
                $sql = $sqlPrepare;
            }

            $users = $wpdb->get_results($sql);

            return array_map(function($user) use ($wpdb){   
                $sqlPrepare = "SELECT * FROM {$wpdb->prefix}usermeta WHERE user_id = %d";
                $sql = $wpdb->prepare($sqlPrepare, $user->ID);
                $userMetaData = $wpdb->get_results($sql);
                
                $userMeta = [];
                foreach($userMetaData as $data){
                    $userMeta[$data->meta_key] = $data->meta_value;
                }

                $user->firstName = $userMeta['first_name'] ?? null;
                $user->lastName = $userMeta['last_name'] ?? null;
                $user->roles = isset($userMeta["{$wpdb->prefix}capabilities"]) ? array_keys(unserialize($userMeta["{$wpdb->prefix}capabilities"])) : ['guest'];

                return WpUser::map($user);
            }, $users);

        }
    }
?>