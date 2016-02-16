<?php get_header(); ?>
<?php colabs_breadcrumbs(array('separator' => '&mdash;', 'before' => ''));?><!-- .colabs-breadcrumbs -->
        
<div class="main-content column col9">
	
  <article <?php post_class('single-entry-post');?>>
    <header class="entry-header">
      <h2 class="entry-title"><?php the_title();?></h2>
    </header>

    <div class="property-details">

      <div class="property-details-panel entry-content">
        <h3 ><?php _e('Pages:','colabsthemes');?></h3>
				<ul>
				<?php wp_list_pages('title_li='); ?>
				</ul>
				<h3 ><?php _e('Categories:','colabsthemes');?></h3>
				<ul>
					<?php wp_list_categories('title_li=&hierarchical=0&show_count=1') ?>
				</ul>
				<h3 ><?php _e('Monthly Archives:','colabsthemes');?></h3>
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>
				<h3 ><?php _e('RSS Feed:','colabsthemes');?></h3>
				<ul>
						<li><a href="<?php bloginfo('rdf_url'); ?>" title="RDF/RSS 1.0 feed"><acronym title="Resource Description Framework">RDF</acronym>/<acronym title="Really Simple Syndication">RSS</acronym> 1.0 feed</a></li>
						<li><a href="<?php bloginfo('rss_url'); ?>" title="RSS 0.92 feed"><acronym title="Really Simple Syndication">RSS</acronym> 0.92 feed</a></li>
						<li><a href="<?php bloginfo('rss2_url'); ?>" title="RSS 2.0 feed"><acronym title="Really Simple Syndication">RSS</acronym> 2.0 feed</a></li>
						<li><a href="<?php bloginfo('atom_url'); ?>" title="Atom feed">Atom feed</a></li>
				</ul>
				<h3 ><?php _e('Property Features:','colabsthemes'); ?></h3>
				<ul><?php wp_list_categories('sort_column=name&optioncount=1&hierarchical=0&taxonomy=property_features'); ?></ul>
				<h3 ><?php _e('Property Type:','colabsthemes'); ?></h3>
				<ul>
				<?php 							
				$args = array(
					'taxonomy'     => 'property_type',
					'orderby'      => 'name',
					'show_count'   => 1,
					'pad_counts'   => 1,
					'hierarchical' => 1,
					'title_li'     => ''
				);
				wp_list_categories($args) 
				?>
				</ul>
				<h3 ><?php _e('Property Location:','colabsthemes'); ?></h3>
				<ul>
				<?php 							
				$args = array(
					'taxonomy'     => 'property_location',
					'orderby'      => 'name',
					'show_count'   => 1,
					'pad_counts'   => 1,
					'hierarchical' => 1,
					'title_li'     => ''
				);
				wp_list_categories($args) 
				?>
				</ul>
      </div>

    </div><!-- .property-details -->

  </article><!-- .single-entry-post -->

</div><!-- .main-content -->

<?php get_sidebar();?><!-- .property-sidebar -->
<?php get_footer(); ?>