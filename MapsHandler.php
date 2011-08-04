<?php
/**
 * Class MapsHandler : To Manage Maps data (add, delete, update Maps, get a Map or several from database)
 **/
class MapsHandler{
	/* getListMaps
	 * @return object sql
	 * @global <type> $wpdb
 	 * @global string $my_config_table 
	 */	
	public function getListMaps(){
		global $wpdb, $my_config_table;
		$sql = "SELECT * from {$my_config_table} ";
		$sql_result=$wpdb->get_results($sql);
		return $sql_result;		
	}
	/* getListPageMaps (for pagination mode)
	 * @param int $action_offset
	 * @param int $per_page
	 * @return object sql
	 * @global <type> $wpdb
 	 * @global string $my_config_table
	 */	
	public function getListPageMaps($action_offset,$per_page){
		global $wpdb, $my_config_table;
		$sql = "SELECT * from {$my_config_table} LIMIT {$action_offset}, {$per_page}";
		$sql_result=$wpdb->get_results($sql);
		return $sql_result;		
	}
	/* getMapById
	 * @param int $id
	 * @return array row sql
	 * @global <type> $wpdb
 	 * @global string $my_config_table
	 */
	public function getMapById($id){
		global $wpdb, $my_config_table;
		$sql = "select * from {$my_config_table} WHERE `id`={$id}";	
		$sql_result=$wpdb->get_row($sql, ARRAY_A);	
		return $sql_result;
	}
	
	/*
	 * addMap add a map
	 * @param string $title_map
	 * @param string $url_map
	 * @return boolean
	 * @global <type> $wpdb
 	 * @global string $my_config_table 
	 */
	public function addMap($title_map,$url_map){
		global $wpdb, $my_config_table;
		$sql_result = $wpdb->insert(
                            $my_config_table,
                            array(
                                'title_map' => $title_map,
                                'url_map' => $url_map                                
                            ),
                            array('%s', '%s'));
		
		return $sql_result;
	}
	/*
	 * updateMap update a map
	 * @param string $title_map
	 * @param string $url_map
	 * @param int $id
	 * @return boolean
	 * @global <type> $wpdb
 	 * @global string $my_config_table
	 */
	public function updateMap($title_map,$url_map,$id){
		global $wpdb, $my_config_table;
		$sql_result = $wpdb->update(
                                $my_config_table,
                                array(
                                    'title_map' => $title_map,                                    
                                    'url_map' => $url_map                                   
                                ),
                                array('id' => $id),
                                array('%s', '%s'),
                                array('%d'));
		return $sql_result;
	}
	
	/*
	 * deleteMap
	 * @param int $id
	 * @return boolean
	 * @global <type> $wpdb
 	 * @global string $my_config_table
	 */
	public function deleteMap($id){
		global $wpdb, $my_config_table;
		$sql = "DELETE FROM {$my_config_table} WHERE id = {$id}";
    	$sql_result = $wpdb->query($sql);
		return $sql_result;
	}
	
		/**
	 * display_map : display a Google Map
	 * @param string Url (http://maps.google.co.nz/maps/ms?msid=...fbd972e&msa=0&output=kml)
	 * @return string HTML content
	 */
	function display_map($url,$width,$height,$id_map) {
		$output .= "<style>		#".$id_map." {
					margin: 2px auto;
					border: 1px dashed #C0C0C0;
					width: ".$width."px;
					height: ".$height."px;
				}
		</style>";
		$output .= '<script type="text/javascript">';
		$output .= '
			$(function() {
					$("#'.$id_map.'").gmap3({
						action : "init",
						options : {
							center : [-41.771312, 172.902344],
							zoom : 5,
							disableDefaultUI : true
						}
					}, {
						action : "addKmlLayer",
						url : "' . $url . '",
						options : {
							suppressInfoWindows : true,
							preserveViewport : false
						}
					});
				});
		';
		$output .= '</script>';
		$output .= '<div id="'.$id_map.'"></div>';
		return $output;
	}
}
?>