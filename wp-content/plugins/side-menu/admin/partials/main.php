<?php if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb;
	$data = $wpdb->prefix . "sidemenu";
	$info = (isset($_REQUEST["info"])) ? $_REQUEST["info"] : '';
	if ($info == "saved") {
		echo "<div class='updated' id='message'><p><strong>Record Added</strong>.</p></div>";
	}
	if ($info == "update") {
		echo "<div class='updated' id='message'><p><strong>Record Updated</strong>.</p></div>";
	}
	if ($info == "del") {
		$delid = $_GET["did"];
		$wpdb->query("delete from " . $data . " where id=" . $delid);
		echo "<div class='updated' id='message'><p><strong>Record Deleted</strong>.</p></div>";
	}
	$resultat = $wpdb->get_results("SELECT * FROM " . $data . " order by id asc");
	$count = count($resultat);
	$tool = (isset($_REQUEST["tool"])) ? sanitize_text_field($_REQUEST["tool"]) : 'list';		
	$tabs = array(
		'list' => array('List','fa-list'), 
		'add' => array('Add new','fa-plus'),
		'style' => array('Style','fa-css3'),
		'pro' => array('Pro version','fa-external-link'),
		'support' => array('Support','fa-life-ring'),
		'facebook' => array('Join Us ','fa-facebook'),
	);  
?>
<div class="wow">
    <span class="wow-plugin-title"><?php echo $name; ?></span> <span class="wow-plugin-version">(version <?php echo $version; ?>)</span>
	<?php echo '<ul class="wow-admin-menu">';
		foreach( $tabs as $tab => $tab_name ){
			$class = ( $tab == $tool ) ? 'active' : '';			
			echo "<li><a class='$class' href='?page=".$pluginname."&tool=$tab'>$tab_name[0] <i class='fa $tab_name[1]'></i></a></li> ";		
		}
		echo '</ul>';
		echo '<p style="padding-bottom: 5px;margin-bottom:30px;"><span class="dashicons dashicons-star-filled"></span>&nbsp;&nbsp; Please rate <a href="https://wordpress.org/plugins/side-menu/reviews/?rate=5#new-post" target="_blank">us on WordPress.org</a>. With your help our products can become better!</p>';
		
		
		
		include_once ($tool.'.php');
		
	?>
</div>