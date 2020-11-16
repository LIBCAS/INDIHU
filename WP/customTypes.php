<?php
    function registerCustomPostTypes(){

        $args = array(
            'label'                 =>  'Zkušenosti uživatelů',
            'labels'                => [
                'name'                  => 'Zkušenosti uživatelů',
                'singular_name'         => 'Zkušenost uživatele',
                'menu_name'             => 'Zkušenosti uživatelů',
                'name_admin_bar'        => 'Zkušenosti uživatelů',
                'all_items'             => 'Přehled zkušeností',
                'add_new_item'          => 'Přidat novou zkušenost',
                'add_new'               => 'Vytvořit zkušenost',
                'edit_item'             => 'Upravit zkušenost',
                'view_item'             => 'Zobrazit zkušenost',
                'view_items'            => 'Zobrazit zkušenost',
                'search_items'          => 'Hledat zkušenost',
                'not_found'             => 'Nebyly nalezeny žádné zkušenosti uživatelů.',
                'not_found_in_trash'    => 'V koši nebyly nalezeny žádné zkušenosti uživatelů.',
                'title'     => ''
            ],
            'supports'              => ['title', 'thumbnail'],
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-admin-site-alt2',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'rewrite' => [
                'slug' => 'zkušenosti uživatelů'
            ],
        );

        register_post_type('user_experience', $args);
    }
         
    add_action('init', 'registerCustomPostTypes', 0);
?>