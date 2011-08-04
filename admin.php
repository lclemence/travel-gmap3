<?php
/**
 * @package travel-gmap3
 */
//Class to manage Maps data from database
require_once 'MapsHandler.php';
/*
 * Add a Map Page
 */
function add_gmap_page() {
    
    if (isset($_POST['save-gmap'])) {
        
        $title_map = $_POST['title_map'];        
        $url_map = $_POST['map_url'];
		$Maps = new MapsHandler();
		$result= $Maps->addMap($title_map,$url_map);        
        if ($result) {
            echo '<div class="updated fade">Map Saved Successfully!!!</div>';
        } else {
            echo '<div class="error">Error Saving Data</div>';
        }
    } 
	?>

    <div class="wrap">
        <h2>Add a map</h2>
        <form method="post" name="add-gmap-form">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="title_map">Title</label></th>
                    <td><input id="title_map" type="text" size="62" name="title_map" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="map_url">URL</label></th>
                    <td><input type="text" value="" name="map_url" id="map_url" size="62"></td>
                    <td><i>Format : http://maps.google.co.nz/maps/ms?msid=...fbd972e&msa=0&output=kml<br>
                        	How? http://maps.google.co.nz > My places > Create new map > (draw lines of your trip) 
                        	> Copy the link here (icon link on top right)
                        	</i></td>
                </tr>
                <tr valign="top">
                    <td></td>
                    <td><input type="submit" value="Save Map" name="save-gmap" class="button-primary"></td>
                </tr>
            </table>
        </form>
    </div>
	<?php
}
/*
 * List Maps Page, edit and delete Maps
 */
function list_gmap_page() {
    
	$Maps = new MapsHandler();
	if ($_REQUEST['page'] === 'list-gmap-page' && $_REQUEST['action'] === 'update'){
		 echo '<div class="updated fade">Map Successfully Edited !!!</div>';
	}
	
	if ($_REQUEST['page'] === 'list-gmap-page' && $_REQUEST['action'] === 'delete'){
		
	    $id = $_GET['id'];
	   
		$sql_result= $Maps->deleteMap($id);
	    if ($sql_result){
	        echo '<div class="updated fade">Map Successfully Deleted !!!</div>';
		}else{
			 echo '<div class="error">Error Saving Data</div>';
		}
	}
	if ($_REQUEST['page'] === 'list-gmap-page' && $_REQUEST['action'] === 'edit') {
        $id = $_GET['id'];
        if (isset($_POST['update-gmap'])) {            
            $title_map = $_POST['title_map'];            
            $url_map = $_POST['url_map'];
            
			$sql_result= $Maps->updateMap($title_map,$url_map,$id);
                if (! $sql_result) {                   
                    echo '<div class="error">Error Saving Data</div>';
                }else{
                	wp_redirect('admin.php?page=list-gmap-page&action=update');
                }
        }       
        
        $sql_result= $Maps->getMapById($id);        
	?>
        <div class ="wrap">
            <h2>Edit Map</h2>

            <form method="post" name="edit-gmap-form">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="title_map">Title</label></th>
                        <td><input type="text" size="35" name="title_map" id="title_map" value="<?php echo $sql_result['title_map'] ?>"></td>
                    </tr>
                    <tr valign="top">
                        <th><label for="url_map">URL</label></th>
                        <td><input type="text" value="<?php echo $sql_result['url_map'] ?>" name="url_map" id="url_map" size="35"></td>
                        <td><i>Format : http://maps.google.co.nz/maps/ms?msid=...fbd972e&msa=0&output=kml<br>
                        	How? http://maps.google.co.nz > My places > Create new map > (draw lines of your trip) 
                        	> Copy the link here (icon link on top right)
                        	</i></td>
                    </tr>
                    <tr valign="top">
                        <td></td>
                        <td><input type="submit" value="Update Map" name="update-gmap" class="button-primary"></td>
                    </tr>
                </table>
            </form>

        </div>
<?php
    } else {
?>
        <div class ="wrap">
            <h2>Map Listing</h2>
            <p>
            	Display one or several maps as follow :<br>
            	<ul><li>In a post with : [travelGmap3 id="2" width="250" height="350"]
            		"id" is the ID of the Map you want to display in the list
            		</li>
            		<li>In a widget (Administration > Appearance > Widget > Drag the widget in your sidebar
            			> select a map and enter width and height)</li>
            </p>
<?php
        $sql_result= $Maps->getListMaps();  
        $pagenum = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $per_page = 5;
        $action_count = count($sql_result);
        $total = ceil($action_count / $per_page);
        $action_offset = ($pagenum - 1) * $per_page;
        $page_links = paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($action_count / $per_page),
                    'current' => $pagenum
                ));
        $sql_result=$Maps->getListPageMaps($action_offset,$per_page);
        $gmaps_ids = $sql_result;

        if (!empty($gmaps_ids)) {
            if ($page_links) {
?>
                <div class="tablenav">
                    <div class="tablenav-pages">
    <?php
                $page_links_text = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s') . '</span>%s',
                                number_format_i18n(( $pagenum - 1 ) * $per_page + 1),
                                number_format_i18n(min($pagenum * $per_page, $action_count)),
                                number_format_i18n($action_count),
                                $page_links
                );
                echo $page_links_text;
    ?>
            </div>
        </div>
            <?php
            }
        }
            ?>
    <div class="clear"></div>
    <?php if (!empty($gmaps_ids)) {
 ?>
            <table class="widefat post fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th class="check-column" scope="row">ID</th>                        
                        <th>Title</th>                        
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody>        
<?php
            $i = 1;
            $count = (($pagenum-1)*$per_page)+1;
            foreach ($gmaps_ids as $gid) {

                if ($i % 2 == 0) {
                    echo '<tr class="alternate">';
                } else {
                    echo '<tr>';
                }
                echo '<td  scope="row" class="check-column">' . $count . '</td>';               
                echo '<td>' . $gid->title_map .
                '<p><a href="admin.php?page=list-gmap-page&action=edit&id=' . $gid->id . '">Edit</a> |
                 <a href="admin.php?page=list-gmap-page&action=delete&id=' . $gid->id . '">Delete</a></p></td>';
                
                echo '<td>' . $gid->url_map . '</td>';
                echo '</tr>';
                $i++;
                $count++;
            }
?>
        </tbody>

    </table>
            <?php } else {
            echo 'No records found!!!';
        } ?>
</div>
<?php
    }
}

?>