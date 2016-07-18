<?php get_header(); ?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->

<?php $field_values = array(); ?>
<?php $field_values['prop_ref'] = $_GET['prop_ref']; ?>
<?php $field_values['prop_type'] = $_GET['prop_type']; ?>

<div class="main-content column col12">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h1 class="entry-title"><?php the_title();?></h1>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content" id="property-details">
            <?php the_content();?>
            <?php gravity_form( 1, $display_title = false, $display_description = false, $display_inactive = false, $field_values, $ajax = false, $tabindex, $echo = true ); ?>
      </div>
    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->
	<?php endwhile;endif;?>
</div><!-- .main-content -->

<?php get_footer(); ?>
