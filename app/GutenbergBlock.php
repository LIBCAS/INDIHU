<?php

    namespace WP;

    class GutenbergBlock{
        
        public static function getBlock() : array{
            $blocks = [
                'UserExperience' => [
                    'slider' => [
                        'attributes' => [
                            'count' => [
                                'type' => 'integer',
                                'default' => 2
                            ]
                        ]
                    ]
                ]
            ];

            return $blocks;
        }

    }