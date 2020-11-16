<?php

    namespace WP;

    class MetaBox{
        
        public static function getMetaBox(){
            $box = [              
                'UserExperience' => [
                    'form' => [
                        'title' => 'Informace',
                        'callback' => 'form',
                        'screen' => 'user_experience',
                        'position' => 'normal',
                        'priority' => 'high'
                    ],
                ]
            ];

            return $box;
        }

    }