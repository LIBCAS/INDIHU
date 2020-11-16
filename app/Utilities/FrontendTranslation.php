<?php
    namespace WP\Utilities;

    use WP\Services\ElasticSearch;

    class FrontendTranslation{
        
        private static $elasticSearchTypes = [
            ElasticSearch::TYPE_POST => 'Články',
            ElasticSearch::TYPE_PAGE => 'Stránky',
            ElasticSearch::TYPE_EMPLOYEE => 'Zaměstnanci',
            ElasticSearch::TYPE_JOB_OFFER => 'Pracovní nabídky',
        ];

        private static $months = [
            '1' => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'
        ];
        
        private static $monthsShort = [
            '1' => 'LED', 'ÚNO', 'BŘE', 'DUB', 'KVĚ', 'ČVN', 'ČVC', 'SRP', 'ZÁŘ', 'ŘÍJ', 'LIS', 'PRO'
        ];

        private static $documentType = [
            'image/jpeg' => 'fa-file-image',
            'image/png' => 'fa-file-image',
            'application/pdf' => 'fa-file-pdf'
        ]; 

        /**
         * @param string $type
         * @param string $value
         * 
         * @return string
         */
        public static function getTranslation(string $type, string $value) : string{
            if(property_exists('WP\Utilities\FrontendTranslation', $type)){
                return self::$$type[$value] ?? '';
            }

            return '';
        }
    }
?>