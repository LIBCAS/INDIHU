<?php
if ( ! function_exists( 'function_widgets_init' ) ) {
	function function_widgets_init() {

		register_sidebar( array(
			'name'          => 'Footer',
			'id'            => 'footer',
			'description'   => '',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		) );
    }
}

add_action( 'widgets_init', 'function_widgets_init' );

?>