<?php 
/*Plugin Name: Event
Description: This an event plug in
Version: 1.0
License: GPLv2
*/

define('base_path_inc', dirname(__FILE__).'/inc');

class plugin_root
{

	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'create_custom_metabox'));
		add_action('save_post', array($this, 'save_metabox_data'));
		add_action('init', array($this, 'create_post_type'));
		add_action('init', array($this, 'create_custom_taxonomy'));
		add_action('admin_menu', array($this, 'event_menu'));
	}

	public function create_post_type()
	{
		register_post_type( 'event', array(
			'labels' =>  array(
		 		'name' => 'Events',
		    	'singular_name' => 'Events',
		    	'add_new' => 'Add New Event',
		    	'add_new_item' => 'Add New Event',
		    	'edit_item' => 'Edit Event',
		    	'new_item' => 'New Event',
		    	'all_items' => 'All Events',
		    	'view_item' => 'View Event',
		    	'search_items' => 'Search Events',
		    	'not_found' =>  'No Events Found',
		    	'not_found_in_trash' => 'No Events found in Trash', 
		    	'menu_name' => 'Events',
		    ),
	 		'public' => true,
			'supports' => array( 'title', 'editor'),
			'rewrite' => array('slug' => 'event'),
			'exclude_from_search' => false,
			'menu_icon' => 'dashicons-format-aside',
		));
	}
	
	

	public function create_custom_taxonomy()
	{
		register_taxonomy('event-types', 'event', array(
			'labels' => array(
				'name' => 'Event Types',
				'add_new_item' => 'Add new Event',
				'parent_item' => 'Parent Event',
			),
			'public' => true,
			'hierarchical' => true,
		));
	}
	
	public function create_custom_metabox()
	{
		add_meta_box(
			'custom_meta_box',
			'Event Details',
			array($this, 'custom_side_meta_box'),
			'event',
			'side'
		);
	}

	public function custom_side_meta_box($post)
	{	
		include_once(base_path_inc.'/datepicker.php');

		$start_date = '';
		if(!empty($post))
		{
			$start_date = get_post_meta($post->ID, 'start_date', true);
		}
		$end_date = '';
		if(!empty($post))
		{
			$end_date = get_post_meta($post->ID, 'end_date', true);
		}
		$city = '';
		$country = '';
		if(!empty($post))
		{
			$city = get_post_meta($post->ID, 'city', true);
			$country = get_post_meta($post->ID, 'country', true);

		}
		?>
		<label>Start date</label>
		<br>
		<input type="text" class="cookie_date" name="start_date" value="<?php if(!empty($start_date)) echo date('m/d/Y', $start_date); ?>">
		<br>
		<label>End date</label>
		<br>
		<input type="text" class="cookie_date" name="end_date" value="<?php if(!empty($end_date)) echo date('m/d/Y', $end_date); ?>">
		<br>
		<label>City</label>
		<br>
		<input type="text" name="city" value="<?php if(!empty($city)) echo $city; ?>">
		<br>
		<label>Country</label>
		<br>
		<input type="text" name="country" value="<?php if(!empty($country)) echo $country; ?>">
		<br>
		<?php
			$text = str_replace(" ", "%20", get_the_title());
			if(!empty($start_date)) $sd = date('Ymd', $start_date);
			if(!empty($end_date)) $ed = date('Ymd', $end_date);
			$description = str_replace(" ", "%20", $post->post_content);
			$city_c = str_replace(" ", "%20", $city);
			$country_c = str_replace(" ", "%20", $country);
		?>
		<a href="http://www.google.com/calendar/event?action=TEMPLATE&text=<?=$text?>&dates=<?=$sd?>/<?=$ed?>&details=<?=$description?>&location=<?=$city_c?>%20<?=$country_c?>" target="_blank" rel="nofollow">Add to my calendar</a>
	<?php
	}

	public function save_metabox_data($post_id)
	{
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		{
			return;
		}

		$slug = 'event';
		if(!isset($_POST['post_type']) || $slug != $_POST['post_type'])
		{
			return;
		}
		if(isset($_POST['city']) && isset($_POST['country']))
		{
			$city = esc_html($_POST['city']);
			$country = esc_html($_POST['country']);

			$response = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?address='.$city.',+'.$country.'&key=AIzaSyBaP8SSS-e3oRCcO5gXI1nJs-RwNm2ZsRI');
			$response_header = wp_remote_retrieve_body($response);
			$decoded_response = json_decode($response_header, true);

			$latitude = $decoded_response['results'][0]['geometry']['bounds']['northeast']['lat'];
			$longitude = $decoded_response['results'][0]['geometry']['bounds']['northeast']['lng'];

			update_post_meta($post_id, 'city', $city);
			update_post_meta($post_id, 'country', $country);
			update_post_meta($post_id, 'latitude', $latitude);
			update_post_meta($post_id, 'longitude', $longitude);
		}
		if(isset($_POST['start_date']))
		{	
			$start_date = esc_html($_POST['start_date']);
			$start_date_str = strtotime($start_date);
			update_post_meta($post_id, 'start_date', $start_date_str);
		}
		if(isset($_POST['end_date']))
		{
			$end_date = esc_html($_POST['end_date']);
			$end_date_str = strtotime($end_date);
			update_post_meta($post_id, 'end_date', $end_date_str);
		}
	}

	public function event_menu()
	{
		add_menu_page('Events View', 'Show all events', 'manage_options', 'view_events', array($this, 'event_menu_callback'), 'dashicons-list-view');
	}

	public function event_menu_callback()
	{
		include_once(base_path_inc.'/template.php');
	}

}

$plugin_root = new plugin_root();

?>