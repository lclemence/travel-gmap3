<?php
/**
 * @package travel-gmap3
 */
require_once 'MapsHandler.php';
// Register my widget class
function travelGmap3_widget_init() {
	register_widget('TravelGmap3Widget');
}
//Do the registration of my class during the init of widgets
add_action('widgets_init', 'travelGmap3_widget_init', 1);
/**
 * Class TravelGmap3Widget : Manage the widget of my plugin
 */
class TravelGmap3Widget extends WP_Widget {
	function TravelGmap3Widget() {
		$widget_ops = array('classname' => 'TravelGmap3Widget',
			'description' => 'Display your Google Map with gmap3');
		
		$this -> WP_Widget('TravelGmap3Widget', 'Travel Map with Gmap3', $widget_ops);
	}

	/**
	 * widget : display a Map in a Widget
	 */
	function widget($args, $instance) {
	   extract($args);
       
       extract(shortcode_atts( array(
                'select_map' => '',
                'width_map' => '', 
                'height_map'=> ''
                ), $instance));	
		$id=(!empty($select_map) ? $select_map : 1);
		$width_map=(!empty($width_map) ? $width_map : 210);
		$height_map=(!empty($height_map) ? $height_map : 350);
		
		$Map=new MapsHandler();	
		$result=$Map->getMapById($id);
		$title = $result['title_map'];
		$mapurl = $result['url_map'];
		
		$id_map=$this -> get_field_id('map');//generate a unique id for the map		
		$output = $Map->display_map($mapurl,$width_map,$height_map,$id_map);
		echo '<div id="'.$this -> get_field_id('widget').'" class="widget">
	 <h4>' . $title . '</h4>' . $output . '</div>';
	}
	/**
	 * update : update a widget (select the map you want in it)
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['select_map'] = strip_tags($new_instance['select_map']);
		$instance['width_map'] = strip_tags($new_instance['width_map']);
		$instance['height_map'] = strip_tags($new_instance['height_map']);
		return $instance;
	}
	/**
	 * form : display the adminstration form of a widget
	 */
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array('select_map' => '','width_map' => '', 'height_map'=> ''));
		$select_map = strip_tags($instance['select_map']);
		$width_map = strip_tags($instance['width_map']);
		$height_map = strip_tags($instance['height_map']);
		$Map=new MapsHandler();	
		$result=$Map->getListMaps();
		

?>
	<p>
	<label for="<?php	echo $this -> get_field_id('select_map');?>"><?php	_e('Select a map:');?></label>
	<select id="<?php	echo $this -> get_field_id('select_map');?>"
	name="<?php	echo $this -> get_field_name('select_map');?>">
		<?php

		foreach($result as $item){
		$id = $item->id;
		$title = $item->title_map;
				?>
				<option value="<?php	echo esc_attr($id);?>" <?php	echo($select_map == $id ? 'selected="selected"' : '');?>><?php	echo esc_attr($title);?></option>
				<?php } ?>
			</select>
		</p>
		<p><label for="<?php	echo $this -> get_field_id('width_map');?>"><?php	_e('Width:');?></label>
		<input type="text" value="<?php echo $width_map;?>" id="<?php	echo $this -> get_field_id('width_map');?>"
		name="<?php	echo $this -> get_field_name('width_map');?>" /></p>	
		<p><label for="<?php	echo $this -> get_field_id('height_map');?>"><?php	_e('Height:');?></label>
		<input type="text" value="<?php echo $height_map;?>" id="<?php	echo $this -> get_field_id('height_map');?>"
		name="<?php	echo $this -> get_field_name('height_map');?>" />
		</p>
		<?php }

}
?>