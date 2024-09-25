<div class="wrap">
	<h1 class="wp-heading-inline">Order</h1><a href="?page=<?php echo esc_attr($_REQUEST['page']); ?>&action=new" class="page-title-action">Add New</a>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
				<form method="post">
				<?php
					$context['orders_obj']->prepare_items();
					$context['orders_obj']->display(); 
				?>
				</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>