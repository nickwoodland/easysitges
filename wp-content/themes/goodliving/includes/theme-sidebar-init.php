<?php

// Register sidebar
if ( ! function_exists('the_widgets_init') ) {
	function the_widgets_init() {
		if( ! function_exists( 'register_sidebar' ) ) {
			return;
		}
		register_sidebar( array( 
			'name' => 'Sidebar Main',
			'id' => 'sidebar',
			'description'	=> 'This widgets will appear inside off canvas sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>\n",
			'before_title' => '<h4 class="widget-title">',
			'after_title' => "</h4>\n"
			)	
		);
		register_sidebar( array( 
			'name' => 'Sidebar User',
			'id' => 'sidebar-user',
			'description'	=> 'This widgets will appear inside off canvas sidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>\n",
			'before_title' => '<h4 class="widget-title">',
			'after_title' => "</h4>\n"
			)	
		);
		register_sidebar( array( 
			'name' => 'Sidebar Footer',
			'id' => 'sidebar-footer',
			'description'	=> 'This widgets will on the footer',
			'before_widget' => '<aside id="%1$s" class="widget %2$s column col3">',
			'after_widget' => "</aside>\n",
			'before_title' => '<h4 class="widget-title">',
			'after_title' => "</h4>\n"
			)	
		);
			
		
	}
}
add_action( 'widgets_init', 'the_widgets_init' );