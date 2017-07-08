<?php
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true);
?>

<script>
	jQuery(document).ready(function(){
	jQuery('.cookie_date').datepicker({
	dateFormat : 'mm/dd/yy'
	});
	});
</script>