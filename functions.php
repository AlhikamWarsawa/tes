<?php
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/plugin.php');
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
$generation_serial = '61.06126731629953';
if ( !function_exists( 'yotako_setup' ) ) :
	function yotako_setup() {
		add_editor_style( 'style.css' );
	}
	add_action( 'after_setup_theme', 'yotako_setup' );
endif;

  add_action( 'wp_enqueue_scripts', function() {
		wp_enqueue_style( 'style', get_theme_file_uri( '/style.css' ), array(), wp_get_theme( 'yotako' )->get( 'Version' ) );
    wp_enqueue_style('Inter', 'https://storage.googleapis.com/yotako-fonts/CdnFonts/css/Inter.css');wp_enqueue_style('Aldrich', 'https://storage.googleapis.com/yotako-fonts/CdnFonts/css/Aldrich.css');    wp_enqueue_script( 'custom-script', get_theme_file_uri() . '/script.js', array(), wp_get_theme( 'yotako' )->get( 'Version' ));
    wp_enqueue_script( 
      'js-alert', 
      'https://unpkg.com/js-alert/dist/jsalert.min.js' 
    );
  });
	function yotako_register_block_patterns() {

		// The block pattern categories included in Yotako.
		$yotako_block_pattern_categories = apply_filters( 'yotako_block_pattern_categories', array(
    	'yotako-general' => array(
				'label'			=> esc_html__( 'Yotako General', 'yotako' ),
			),
			'yotako-footer' => array(
				'label'			=> esc_html__( 'Yotako Footer', 'yotako' ),
			),
			'yotako-header' => array(
				'label'			=> esc_html__( 'Yotako Header', 'yotako' ),
      ),
      'yotako-menu' => array(
				'label'			=> esc_html__( 'Yotako Menu', 'yotako' ),
      ),
      'yotako-post_list' => array(
				'label'			=> esc_html__( 'Yotako Post_List', 'yotako' ),
      ),
		) );

		// Sort the block pattern categories alphabetically based on the label value, to ensure alphabetized order when the strings are localized.
		uasort( $yotako_block_pattern_categories, function( $a, $b ) { 
			return strcmp( $a["label"], $b["label"] ); }
		);

		// Register block pattern categories.
		foreach ( $yotako_block_pattern_categories as $slug => $settings ) {
			register_block_pattern_category( $slug, $settings );
		}
	
	}

function reset_permalinks() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules(true);
}
 function add_pages(){
    global $generation_serial;
   $pages = array(array("name" => "Home_Dark","slug" => "Home_Dark", "mainPage"=>true),);
      foreach ($pages as $page) {
        $args = array(
            "meta_key" => "slug",
            "meta_value" => $page['slug'],
            'post_type' => 'page',
            'post_status' => 'publish',
        );
          $existedPosts = get_pages($args);
          if($existedPosts[0]->ID):
            wp_trash_post($existedPosts[0]->ID);
          endif;    
          $pageArr = array(
              'post_title'    => $page['name'],
              'page_template'  => $page['name'],
              'post_type'   => 'page',
              'post_status' =>  'publish',
              'meta_input' => array(
                'slug' => $page['slug'],
                'serial_number' => $generation_serial
              ),
            );
          $p = wp_insert_post( $pageArr );
          if($page["mainPage"] == true ):
            update_option( 'page_on_front', $p );
            update_option( 'show_on_front', 'page' );
          endif;
      }
 }
 function add_menus(){
              
    }

 if ( !function_exists( 'yotako_theme_support' ) ) {
	function yotako_theme_support() {
    add_theme_support('menus');
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );
	}
}

function plugin_installation(){
    $site_url= get_site_url();
    $base_plugin_url = "https://tako.assets.yotako.io/plugin/"; // url to zip file
    $plugins = array("yotako-wordpress-basics",);
    // Only run when it's not on wasm
  if (!str_contains($site_url, 'https://wp-playground.yotako') && !str_contains($site_url, 'https://playground.wordpress.net')) {
      $plugin_path = WP_CONTENT_DIR."/plugins/";
    	$errors = array();
      foreach ( $plugins as $plugin ) {
        if(!is_dir($plugin_path.$plugin)){
          mkdir($plugin_path.$plugin);
        }
      $file = $plugin_path.$plugin."/".$plugin.".zip";
      // download the file 
        file_put_contents($file, fopen($base_plugin_url.$plugin.".zip", 'r'));
        // extract file content 
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME); 
        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
          $zip->extractTo($path);
          $zip->close();
        } 
      }
      activate_yotako_plugins($plugins);
      if ( ! empty( $errors ) ) {
        return new WP_Error( 'plugins_invalid', __( 'One of the plugins is invalid.' ), $errors );
      }
  }
  
}
function activate_yotako_plugins($plugins){
  foreach ( $plugins as $plugin ) {
    $result = activate_plugin(WP_CONTENT_DIR."/plugins/".$plugin."/".$plugin.".php" );
     if ( is_wp_error( $result ) ) {
      $errors[ $plugin ] = $result;
    }
  }
}

function remove_admin_bar() {
  $site_url= get_site_url();
  if (!str_contains($site_url, 'https://wp-playground.yotako') && !str_contains($site_url, 'https://playground.wordpress.net') && current_user_can('administrator')) {
    add_filter( 'show_admin_bar', '__return_true' );
  }else{
    add_filter( 'show_admin_bar', '__return_false' );

  }
}

function get_global_script() {
  wp_enqueue_script( 'script', 'https://tako.assets.yotako.io/js/yotako-global.js');
}


function remove_demo_menu() {
    remove_submenu_page( 'tools.php', 'export.php' );
    remove_submenu_page( 'tools.php', 'export-personal-data.php');
  }
function remove_default_endpoints_smarter( $endpoints ) {
    $prefix = '/wp-block-editor/v1/export';
     foreach ( $endpoints as $endpoint => $details ) {
      if ( fnmatch($prefix, $endpoint, FNM_CASEFOLD ) ) {
        unset( $endpoints[$endpoint] );
      }
    }
    return $endpoints;
  
  }
  $site_url= get_site_url();
 if (str_contains($site_url, 'https://wp-playground.yotako') || str_contains($site_url, 'https://playground.wordpress.net') || str_contains($site_url, 'https://wordpress.org/playground')) {
    add_action( 'admin_menu', 'remove_demo_menu' );
    add_action('after_setup_theme', 'get_global_script');
    add_filter( 'rest_endpoints', 'remove_default_endpoints_smarter' );
  }
function initial_theme(){
  add_pages();
  reset_permalinks();
  add_menus();
  yotako_theme_support();
  plugin_installation();
  yotako_register_block_patterns();
}
remove_all_filters("content_save_pre");
remove_all_filters("pre_content");
remove_all_filters("pre_post_content");
remove_all_filters("content_pre ");
add_action('after_switch_theme', 'initial_theme');
add_action('after_setup_theme', 'remove_admin_bar');
remove_filter( 'the_content', 'wpautop' );
?>