<?php
    namespace WP\Client\Resources;

    use WP\Utilities\PageRender;
    use WP\Models\UserModel;

    class Base{

        private $user;

        /**
         * @param UserModel $userModel
         */
        public function __construct(UserModel $userModel){
            $userModel = $userModel;
            $this->user = $userModel->getCurrentUser();
        }

        /**
         * @param string $title
         * @return void
         */
        protected function notFound(string $title = null) : void{
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
            PageRender::loadRender('404', CLIENT_TEMPLATE_DIR . '/wp/page/404.latte', 'Stránka nebyla nalezena', []);
        }

        /**
         * @return void
         */
        protected function forbidden() : void{
            status_header(403);
            nocache_headers();
            wp_die('forbidden');
            // PageRender::loadRender('403', CLIENT_TEMPLATE_DIR . '/wp/page/403.latte', 'Neautorizovaný přístup', []);
        }

        /**
         * @param array|object $data
         * @return void
         */
        protected function sendJson($data, $type = null){

            if(is_array($data)){
                $dataToJson = array_map(function($item) {
                    return $item->jsonSerialize($type);
                }, $data);
            }else{
                $dataToJson = $data->jsonSerialize($type);
            }

            \Tracy\Debugger::$productionMode = TRUE;

            header('Content-type:application/json;charset=utf-8');
            echo json_encode($dataToJson); exit;
        }

        /**
         * @param mixed $data
         * @return void
         */
        protected function sendJsonData($data){
            \Tracy\Debugger::$productionMode = TRUE;

            header('Content-type:application/json;charset=utf-8');
            echo json_encode($data); exit;
        }

        /**
         * @param string $filename
         * @param array $data
         * @return void
         */
        protected function sendCsv(string $filename, array $data) : void{
            \Tracy\Debugger::$productionMode = TRUE;

            header("content-type:application/csv;charset=UTF-8");
            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");

            echo $data; exit;
        }

        /**
         * @param array $data
         * @return array
         */
        protected function arraySerialize(array $data) : array{
            $jsonArray = array_map(function($item) {
                return $item->jsonSerialize();
            }, $data);

            return $jsonArray;
        }

        /**
         * @param string $templateName
         * @param array $param
         * @return string
         */
        protected function renderShortcode(string $templateName, array $param) : string{
            ob_start();

            $assetsDir = get_template_directory_uri() . '/dist';
            $param['img_dir'] = $assetsDir . '/images';

            render($templateName, $param);
            $shortcode = ob_get_clean();
            return $shortcode;
        }

        /**
         * @return WpUser|null
         */
        protected function getUser() : ?WpUser{
            return $this->user;
        }

        /**
         * @return boolean
         */
        protected function isLoggedIn() : bool{
            return $this->user !== null;
        }

        // protected function sendApiData($data, $type = null){
        //     if(is_array($data)){
        //         $responseData = array_map(function($item) {
        //             return $item->jsonSerialize($type);
        //         }, $data);
        //     }else{
        //         $responseData = $data->jsonSerialize($type);
        //     }
            
        //     return new \WP_REST_Response($responseData, 200);
        // }

        protected function sendApiJsonData($responseData){
            return new \WP_REST_Response($responseData, 200);
        }
        
        // protected function sendApiError(){
        //     return new \WP_Error( 'Not Found', 'Posouzení neexistuje', ['status' => 404]);
        // }
    }
?>