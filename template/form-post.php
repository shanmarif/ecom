<?php
if($context['intent-type'] == "redirect"){
	wp_redirect($context['url']);exit;
} elseif(strtolower($context['intent-type']) == "post"){ ?>
	<form name="checkoutform" action="<?php echo $context['url']; ?>" method="post">
		<?php foreach($context['data'] as $name => $value){ ?>
			<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
		<?php } ?>
	</form>
	<script type="text/javascript">
		window.onload = function(){
			document.checkoutform.submit();
		}
	</script>
<?php } ?>