<?php get_header(); ?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
        
<div class="main-content column col9">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>	
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h1 class="entry-title"><?php the_title();?></h1>
      <p class="entry-meta">
				<span><i class="icon-user"></i><?php the_author_posts_link(); ?></span>
				<time><i class="icon-calendar"></i><?php the_time(get_option('date_format')); ?></time>
				<span><i class="icon-list"></i><?php the_category(', ');?></span>
				<span><i class="icon-comments"></i><a class="comment-count" href="<?php comments_link(); ?>" title=""><?php comments_number( __('0 Comment','colabsthemes'), __('1 Comment','colabsthemes'), __('% Comments','colabsthemes') ); ?></a></span>
			</p>
    </header>

		<div class="entry-media">
			<?php   
				$single_top = get_post_custom_values("colabs_single_top");
				if (($single_top[0]!='')||($single_top[0]=='none')) {
								
				if ($single_top[0]=='single_video'){
					$embed = colabs_get_embed('colabs_embed',664,350,'single_video',$post->ID);
					if ($embed!=''){
						echo $embed; 
					}
				} elseif($single_top[0]=='single_image') {
					colabs_image('width=664');				
				}										
			} ?>
		</div>

		<div class="entry-content">
			<?php the_content();?>
			<div class="entry-tags"><?php the_tags();?></div>
			<?php echo colabs_share(); ?>
			<?php wp_link_pages( array(
					'before' => '<p class="single-post-pagination">' . __('Pages:', 'colabsthemes')
				) ); ?>
			<?php colabs_ad_gen();?>
		</div>

  </article><!-- .single-entry-post -->

	<div class="comment-block">
		<?php comments_template( '', true ); ?>
	</div>

	<?php endwhile;endif;?>
</div><!-- .main-content -->

<?php get_sidebar();?><!-- .property-sidebar -->
<?php get_footer(); ?>