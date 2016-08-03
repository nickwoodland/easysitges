<?php get_header(); ?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
        
<div class="main-content column col9">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>	
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h1 class="entry-title"><?php the_title();?></h1>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content" id="property-details">
                <?php the_content();?>
      </div>

      <?php comments_template( '', true ); ?>
    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->
	<?php endwhile;endif;?>
</div><!-- .main-content -->

<?php get_sidebar();?><!-- .property-sidebar -->
<?php get_footer(); ?>