<?php
/**
* Template name: New Map
 */
 get_header();
 ?>

<?php 
    $posts = new WP_Query(array(
    'post_type' => 'event',
    'meta_key' => 'end_date',
    'orderby' => 'meta_value_num', 
    'order' => 'DESC'
     ));
    $i = 0;
    while($posts->have_posts()): $posts->the_post();
    $latitude[] = get_post_meta(get_the_ID(), 'latitude', true);
    $longitude[] = get_post_meta(get_the_ID(), 'longitude', true);
    $city[] = get_post_meta(get_the_ID(), 'city', true);
    $countries[] = get_post_meta(get_the_ID(), 'country', true);
?>
<h1>Event: <?=the_title()?></h1>
<h3>Event location</h3>
<div id="<?=$city[$i]?>" style="height: 400px; width: 50%;"></div>

<?php
$i++; 
endwhile;
?>

<script>
  function initMap() {
  	<?php 
  	$i = 0; 
  	foreach($countries as $country){
  		if(!empty($latitude[$i]) && !empty($longitude[$i])){
  	?>
    var <?=$country?> = {lat:  <?=$latitude[$i]?>, lng: <?=$longitude[$i]?>};
    var <?=$city[$i]?> = new google.maps.Map(document.getElementById('<?=$city[$i]?>'), {
      zoom: 8,
      center: <?=$country?>
    });
    var marker = new google.maps.Marker({
      position: <?=$country?>,
      map: <?=$city[$i]?>
    });
    <?php $i++; }} ?>
  }
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaP8SSS-e3oRCcO5gXI1nJs-RwNm2ZsRI&callback=initMap">
</script>
<?php
wp_reset_query();
get_footer();
?>