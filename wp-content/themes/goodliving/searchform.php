<?php
/**
 * Search form template
 */
?>

<form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>">
	<div>
		<label class="screen-reader-text" for="s"><?php _e('Search for:','colabsthemes');?></label>
		<input type="text" value="" name="s" id="s" placeholder="<?php _e('Search', 'colabsthemes'); ?>">
		<?php if( is_404() ) : ?>
			<input class="button" type="submit" value="<?php esc_html_e( 'Search', 'colabsthemes' ); ?>">
		<?php endif; ?>
	</div>
</form>