<?php $class = colabs_image('return=true') ? 'have-image' : 'no-image'; ?>
<article class="entry-post property <?php echo $class; ?>">
    <div class="entry-inner">
			<?php colabs_image('width=300&single=true&before=<figure class=entry-media>&after=</figure>');?>
      <header class="entry-header">
        <h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
      </header>
      <footer class="entry-footer clearfix">
        <?php the_time(get_option('date_format')); ?></span>&nbsp;/&nbsp;
				<a class="comment-count" href="<?php comments_link(); ?>" title=""><?php comments_number( __('No Comment','colabsthemes'), __('1 Comment','colabsthemes'), __('% Comments','colabsthemes') ); ?></a>
      </footer>
    </div>
</article>
	