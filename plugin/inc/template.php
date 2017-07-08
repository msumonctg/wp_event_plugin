<?php
$posts = new WP_Query(array(
				'post_type' => 'event',
				'meta_key' => 'end_date',
				'orderby' => 'meta_value_num', 
				'order' => 'DESC'
				));
?>
<h1>Event List</h1>
<table border="1">
	<tr>
		<th>Event</th>
		<th>End date</th>
	</tr>
	<?php while($posts->have_posts()): $posts->the_post();?>
		<tr>
			<td>
				<?=the_title();?>
			</td>
			<td>
			 <?php
			 	$get_end_date = get_post_meta(get_the_ID(), 'end_date', true);
			 	if(!empty($get_end_date))
			 	{
				 	$end_date = date('m/d/Y', $get_end_date);
				 	echo $end_date;
			 	}
			 ?>
			</td>
		</tr>
	<?php endwhile;?>
</table>