<?php
/**
 * General
 *
 * This file contains any general functions
 *
 * @package      Jobera_Functionality
 * @since        1.0.0
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
 
/**
 * Don't Update Plugin
 * @since 1.0.0
 * 
 * This prevents you being prompted to update if there's a public plugin
 * with the same name.
 *
 * @author Mark Jaquith
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array $r, request arguments
 * @param string $url, request url
 * @return array request arguments
 */
// function be_core_functionality_hidden( $r, $url ) {
// 	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
// 		return $r; // Not a plugin update request. Bail immediately.
// 	$plugins = unserialize( $r['body']['plugins'] );
//   var_dump($plugins);
// 	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
// 	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
// 	$r['body']['plugins'] = serialize( $plugins );
// 	return $r;
// }
// add_filter( 'http_request_args', 'be_core_functionality_hidden', 5, 2 );

// Use shortcodes in widgets
add_filter( 'widget_text', 'do_shortcode' );


function get_headers_curl($url) { 
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,            $url);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT,        10);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

  $r = curl_exec($ch);

  return $r; 
}

function vh_get_html_curl($url) { 
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,            $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER,         0);
  curl_setopt($ch, CURLOPT_TIMEOUT,        10);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

  $r = curl_exec($ch);

  return $r; 
}

/**
 * Remove Menu Items
 * @since 1.0.0
 *
 * Remove unused menu items by adding them to the array.
 * See the commented list of menu items for reference.
 *
 */
function be_remove_menus () {
	global $menu;
	$restricted = array(__('Links'));
	// Example:
	//$restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'));
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
}
add_action( 'admin_menu', 'be_remove_menus' );

/**
 * Customize Admin Bar Items
 * @since 1.0.0
 * @link http://wp-snippets.com/addremove-wp-admin-bar-links/
 */
function be_admin_bar_items() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'new-link', 'new-content' );
}
add_action( 'wp_before_admin_bar_render', 'be_admin_bar_items' );


/**
 * Customize Menu Order
 * @since 1.0.0
 *
 * @param array $menu_ord. Current order.
 * @return array $menu_ord. New order.
 *
 */
function be_custom_menu_order( $menu_ord ) {
	if ( !$menu_ord ) return true;
	return array(
		'index.php', // this represents the dashboard link
		'edit.php?post_type=page', //the page tab
		'edit.php', //the posts tab
		'edit-comments.php', // the comments tab
		'upload.php', // the media manager
    );
}
//add_filter( 'custom_menu_order', 'be_custom_menu_order' );
//add_filter( 'menu_order', 'be_custom_menu_order' );

/**
 * Pretty Printing
 * 
 * @author Chris Bratlien
 *
 * @param mixed
 * @return null
 */
function be_pp( $obj, $label = '' ) {  

	$data = json_encode(print_r($obj,true));
    ?>
    <style type="text/css">
      #bsdLogger {
      position: absolute;
      top: 30px;
      right: 0px;
      border-left: 4px solid #bbb;
      padding: 6px;
      background: white;
      color: #444;
      z-index: 999;
      font-size: 1.25em;
      width: 400px;
      height: 800px;
      overflow: scroll;
      }
    </style>    
    <script type="text/javascript">
      var doStuff = function(){
        var obj = <?php echo $data; ?>;
        var logger = document.getElementById('bsdLogger');
        if (!logger) {
          logger = document.createElement('div');
          logger.id = 'bsdLogger';
          document.body.appendChild(logger);
        }
        ////console.log(obj);
        var pre = document.createElement('pre');
        var h2 = document.createElement('h2');
        pre.innerHTML = obj;
 
        h2.innerHTML = '<?php echo addslashes($label); ?>';
        logger.appendChild(h2);
        logger.appendChild(pre);      
      };
      window.addEventListener ("DOMContentLoaded", doStuff, false);
 
    </script>
    <?php
}

/**
 * Disable WPSEO Nag on Dev Server 
 *
 */
function be_disable_wpseo_nag( $options ) {
	if( strpos( site_url(), 'localhost' ) || strpos( site_url() ,'master-wp' ) )
		$options['ignore_blog_public_warning'] = 'ignore';
	return $options;
}
add_filter( 'option_wpseo', 'be_disable_wpseo_nag' );

// Disable WPSEO columns on edit screen 
add_filter( 'wpseo_use_page_analysis', '__return_false' );