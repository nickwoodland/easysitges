<?php
/**
 * Sidebar Footer Template
 */
?>
<div class="footer-widgets">
  <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-footer') ) :  ?>
	<?php endif; ?>
</div>