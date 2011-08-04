<?php 
/**
 * @package travel-gmap3
 */
/*
Plugin Name: Travel Map with Gmap3
Plugin URI: 
Description: travel-gmap3 is an open source a solution built for Wordpress to display your Google Maps with the JQuery plugin Gmap3 on your Wordpress as a Widget or in a post. (You have to draw your trip on Google Maps and copy the link on the plugin administration) 
Version: 1
Author: Clemence Lelong
Author URI: 
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
//global variable for database access
global $wpdb, $my_config_table;
$my_config_table = $wpdb->base_prefix . 'travel_gmap3_config';
//Define a widget class for my plugin
include_once dirname( __FILE__ ) . '/widget.php';

//Define installation process 
register_activation_hook(__FILE__, 'travelGmap3_install');
/**
 * travelGmap3_install
 * @global <type> $wpdb
 * @global string $my_config_table
 */
function travelGmap3_install() {
    global $wpdb, $my_config_table;
    
    if (!empty($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    if (!empty($wpdb->collate))
        $charset_collate .= " COLLATE $wpdb->collate";

    $sql = "CREATE TABLE {$my_config_table} (
                            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,                            
                            `title_map` varchar(200),                            
                            `url_map` varchar(200)                            
                            ) {$charset_collate};";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


//Define uninstallation process
register_deactivation_hook(__FILE__, 'travelGmap3_uninstall_hook');
 /**
 * travelGmap3_uninstall_hook
 * @global <type> $wpdb
 * @global string $my_config_table
 */
function travelGmap3_uninstall_hook()
{
    global $wpdb, $my_config_table;
    $sql = "DROP TABLE {$my_config_table};";
    $wpdb->query($sql);   
}

//Define library in Wordpress
add_action('init', 'travelGmap3_init');
/**
 * travelGmap3_init
 */
function travelGmap3_init() {
		//JQuery 1.6.1	
		wp_deregister_script('jquery');
		wp_register_script('jquery', plugins_url('jquery-1.6.1.min.js',__FILE__)); 
		wp_enqueue_script('jquery');
		// Google Maps API v3
		wp_deregister_script('googlemapsapi3');
		wp_register_script('googlemapsapi3', 'http://maps.google.com/maps/api/js?sensor=false&key=ABQIAAAA-8YtxHRtdSJFyoTuSinbGBQAPvlerzS_1sXNKiRzTY0FEFpVyhTrig6Syfv6sBafXVUEkYao0hXjBg', false, '3'); 
		wp_enqueue_script('googlemapsapi3');
		// gmap itself
		wp_deregister_script('gmap3');
		wp_register_script('gmap3', plugins_url('gmap3.min.js',__FILE__), array('jquery'), '3.4'); 
		wp_enqueue_script('gmap3');		
}  
 
if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';
//Define adminstration menu in Wordpress
	add_action('admin_menu', 'travelGmap3_admin_menu');
/*
 * Administration Menu
 */	
function travelGmap3_admin_menu() {
    add_object_page('Travel Map with Gmap3', 'Travel Map with Gmap3', 'manage_options', 'list-gmap-page');
    add_submenu_page('list-gmap-page', 'List Map', 'List maps', 'manage_options', 'list-gmap-page', 'list_gmap_page');
    add_submenu_page('list-gmap-page', 'Add New Map', 'Add New map', 'manage_options', 'add-gmap-page', 'add_gmap_page');
   
}

//Add the map in a post when there is [travelGmap3]
require_once dirname( __FILE__ ) . '/MapsHandler.php';
add_shortcode( 'travelGmap3', 'travelGmap3_show_on_post' ); 
/**
 * travelGmap3_show_on_post : display a map in a post when there is [travelGmap3]
 * @param array All the attribute in [travelGmap3]
 * @return string HTML
 */
function travelGmap3_show_on_post($atts, $content=NULL){
	//$atts contains the attribute ->	[travelGmap3 id="2" width="250" height="350"]
	//id is the identifiant of the map you want to display
	extract(shortcode_atts( array(
                'id' => '1',
                'width' => '210',
                'height' => '350'
                ), $atts));			
	if (! empty($id)){
		$idDB=(! empty($id) ? $id : '');	
		$width_map=(! empty($width) ? $width : '210');
		$height_map=(! empty($height) ? $height : '350');
		$id_map="travelGmap3_".$idDB;	 
		
		$Map = new MapsHandler();
		$result=$Map->getMapById($idDB);	
		$mapurl = $result['url_map'];
		$output = $Map->display_map($mapurl,$width_map,$height_map,$id_map);
	}else{
		$output="No map found!";
	}
	
	return $output;
}

?>