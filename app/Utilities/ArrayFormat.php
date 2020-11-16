<?php

    namespace WP\Utilities;

    class ArrayFormat{

        /**
         * @param array $array
         * 
         * @return array
         */
        public static function editArrayFormatForSelect(array $array) : array{
            $result = [];
            foreach($array as $item){
                $result[$item->getId()] = $item->getName();
            }
            return $result;
        }

        /**
         * @param array $array
         * 
         * @return array
         */
        public static function editTaxonomyArrayFormatForSelect(array $array) : array{
            $result = [];
            foreach($array as $item){
                $result[$item->getSlug()] = $item->getName();
            }
            return $result;
        }

        /**
         * @param array $array
         * 
         * @return array
         */
        public static function idFromDbToSimpleArrayForSelect(array $array) : array{
            $result = [];
            foreach($array as $item){
                $result[] = $item->id;
            }
            return $result;
        }

        public static function addItemToAssociativeArrayBeforeKey(string $beforeKey, array $array, string $itemKey, string $itemValue){
            if($beforeKeyIndex = array_search($beforeKey, array_keys($array))){
                $end = array_slice($array, $beforeKeyIndex);
                $array = array_slice($array, 0, $beforeKeyIndex);
            }
        
            $array[$itemKey] = $itemValue;
        
            return isset($end) ? array_merge($array, $end) : $array;
        }
    }
?>