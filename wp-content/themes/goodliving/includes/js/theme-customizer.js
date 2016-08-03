( function( $ ){
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( 'h1.logo a' ).html( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-tagline' ).html( to );
		} );
	} );
} )( jQuery );