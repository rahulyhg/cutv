<?php
	/* Require Ajax WP load */
	if( isset( $_GET[ 'wpvr_wpload' ] ) || isset( $_POST[ 'wpvr_wpload' ] ) ) {
		define( 'DOING_AJAX' , TRUE );
		//define('WP_ADMIN', true );
		$wpload = 'wp-load.php';
		while( ! is_file( $wpload ) ) {
			if( is_dir( '..' ) ) chdir( '..' );
			else die( 'EN: Could not find WordPress! FR : Impossible de trouver WordPress !' );
		}
		require_once( $wpload );
	}
	
	global $wpvr_addons , $wpvr_all_plugins , $is_reloading , $wpvr_pages;
	$wpvr_pages = TRUE;
	$addons     = array( 'installed' => array() , 'not_installed' => array() );
	
	if( isset( $_GET[ 'is_reloading' ] ) ) $addons_obj = wpvr_get_addons( array() , TRUE );
	else $addons_obj = wpvr_get_addons( array() , FALSE );
	
	//d( $addons_obj );
	
	if( ! isset( $addons_obj[ 'msg' ] ) ) $last_refresh = '';
	else $last_refresh = $addons_obj[ 'msg' ];
	//update_option( 'wpvr_addons_list' , '');
	
	if( $addons_obj[ 'status' ] === FALSE ) {
		?>
		<div class = "error">
			<p>
				<b><?php _e( 'WP Video Robot ERROR' , WPVR_LANG ); ?></b> : <br/>
				<?php echo $addons_obj[ 'msg' ]; ?>
				<br/>
			</p>
		</div>
		<?php
		return FALSE;
	}
	global $available_addons;
	$available_addons = (array)$addons_obj[ 'items' ];
	$available_addons = apply_filters( 'wpvr_extend_addons_list' , $available_addons );
	//new dBug( $available_addons );
	//d( $available_addons );

	foreach( $available_addons as $k => $addon ) {
		$addon = (array)$addon;
		
		$id = $addon[ 'id' ];
		
		if( ! isset( $addon[ 'led' ] ) ) $addon[ 'led' ] = FALSE;
		
		if( isset( $wpvr_all_plugins[ $addon[ 'plugin_dir' ] ] ) ) {
			
			$addon[ 'is_installed' ]   = TRUE;
			$addon[ 'is_active' ]      = is_plugin_active( $addon[ 'plugin_dir' ] );
			$addon[ 'activate_url' ]   = admin_url() . 'admin.php?page=wpvr-addons&activate_addon=' . $addon[ 'plugin_dir' ];
			$addon[ 'deactivate_url' ] = admin_url() . 'admin.php?page=wpvr-addons&deactivate_addon=' . WPVR_PLUGINS_PATH . $addon[ 'plugin_dir' ];
			
			if( $addon[ 'is_active' ] ) $addon[ 'link_url' ] = admin_url() . 'admin.php?page=' . $addon[ 'id' ];
			else $addon[ 'link_url' ] = $addon[ 'addon_url' ];
			//d( $addon );
			$addons[ 'installed' ][ $id ] = $addon;
		} else {
			$addon[ 'is_installed' ]          = FALSE;
			$addon[ 'is_active' ]             = FALSE;
			$addon[ 'activate_url' ]          = '#';
			$addon[ 'deactivate_url' ]        = '#';
			$addon[ 'link_url' ]              = $addon[ 'addon_url' ];
			$addons[ 'not_installed' ][ $id ] = $addon;
		}
		
	}
	//d( $addons );
	//return false;
	$active = array(
		'installed'     => '' ,
		'not_installed' => '' ,
		'all'           => '' ,
	);
	
	if( ! isset( $_GET[ 'section' ] ) || ! isset( $active[ $_GET[ 'section' ] ] ) ) {
		if( count( $addons[ 'installed' ] ) != 0 ) $active[ 'installed' ] = 'active';
		else $active[ 'not_installed' ] = 'active';
	} else {
		
		$active[ $_GET[ 'section' ] ] = 'active';
	}
	
	//new dBug( $wpvr_all_plugins );
	$reloadLink = admin_url() . 'admin.php?page=wpvr-addons&reload_addons_list';

?>
<div class = "wpvr_addons_wrapper" style = "">
	
	<!-- TABS -->
	<div class = "wpvr_nav_tabs pull-left">
		<div class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'installed' ]; ?>" id = "a">
			<i class = "wpvr_tab_icon fa fa-check-circle"></i><br/>
			<span><?php _e( 'Installed Addons' , WPVR_LANG ); ?></span>
		</div>
		<div class = "wpvr_nav_tab pull-left noMargin <?php echo $active[ 'not_installed' ]; ?>" id = "b">
			<i class = "wpvr_tab_icon fa fa-th-large"></i><br/>
			<span><?php _e( 'Browse Addons' , WPVR_LANG ); ?></span>
		</div>
		<a
			class = "wpvr_reload_link pull-right"
			href = "<?php echo $reloadLink; ?>"
			title = "<?php echo $last_refresh; ?>"
			>
			Reload
		</a>

	</div>
	<!-- TABS -->

	<!-- INSTALLED ADDONS -->
	<div id = "" class = "wpvr_nav_tab_content tab_a">
		<?php //wpvr_render_addons_offers(); ?>
		<?php wpvr_render_addons_list( $addons[ 'installed' ] , TRUE ); ?>
	</div>
	<!-- INSTALLED ADDONS -->
	
	<!-- BROWSE ADDONS -->
	<div id = "" class = "wpvr_nav_tab_content tab_b">
		<?php wpvr_render_addons_offers(); ?>
		<?php wpvr_render_addons_list( $addons[ 'not_installed' ] , FALSE ); ?>
	</div>
	<!-- BROWSE ADDONS -->
	
	
	<div class = "wpvr_clearfix"></div>
</div>