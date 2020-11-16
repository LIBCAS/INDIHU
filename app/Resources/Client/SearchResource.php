<?php
    namespace WP\Client\Resources;

    use WP\Services\ElasticSearch;

    class SearchResource extends Base{

        /** @var ElasticSearch */
        private $elasticSearch;

        public function __construct(ElasticSearch $elasticSearch){
            $this->elasticSearch = $elasticSearch;
        }

        public function actionApiSearchAutocomplete(){
            $response = [
                'items' => $this->elasticSearch->searchAutocomplete($_GET['searchText'])
            ];

            $this->sendJsonData($response);
        }
        
    }

?>