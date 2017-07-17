<?php
/*
    Plugin Name: WP Accessibility Helper
    Plugin URI: http://accessibility-helper.co.il
    Description: WP Accessibility Helper sidebar
    Author: Alexander Volkov
    Version: 0.5.8.1
    Author URI: http://www.volkov.co.il
    License: GPL2
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
    Text Domain: wp-accessibility-helper
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include_once( dirname(__FILE__)  . '/inc/wah-front-functions.php');

function wah_admin() {
    include("admin/pages/wah-admin.php");
}
function wah_attachments() {
    include("admin/pages/wah-attachments.php");
}
function wah_landmark() {
    include("admin/pages/wah-landmark.php");
}
function wah_contribute() {
    include("admin/pages/wah-contribute.php");
}
function wah_dom_scanner() {
    include("admin/pages/wah-dom-scanner.php");
}
function wah_sidebar_controls() {
    include("admin/pages/wah-sidebar-controls.php");
}

function wp_accessibility_helper_admin_actions() {
    add_menu_page(
        __( 'Accessibility', 'wp-accessibility-helper' ),
        'Accessibility','manage_options','wp_accessibility','wah_admin','dashicons-universal-access-alt'
    );
    add_submenu_page(
      	'wp_accessibility',
        __( 'Widgets Order', 'wp-accessibility-helper' ),'Widgets Order','manage_options','wp_accessibility_sidebar_controls','wah_sidebar_controls'
  	);
    add_submenu_page(
      	'wp_accessibility',
        __( 'DOM Scanner', 'wp-accessibility-helper' ),'DOM Scanner','manage_options','wp_accessibility_dom_scanner','wah_dom_scanner'
  	);
    add_submenu_page(
    	'wp_accessibility',
        __( 'Attachments Control', 'wp-accessibility-helper' ),'Attachments Control','manage_options','wp_accessibility_image','wah_attachments'
	);
    add_submenu_page(
    	'wp_accessibility',
        __( 'Landmark & CSS', 'wp-accessibility-helper' ),'Landmark & CSS','manage_options','wp_accessibility_landmark','wah_landmark'
	);
    add_submenu_page(
      	'wp_accessibility',
        __( 'Contribute', 'wp-accessibility-helper' ),'Contribute','manage_options','wp_accessibility_contribute','wah_contribute'
  	);
}
add_action('admin_menu', 'wp_accessibility_helper_admin_actions');
/*********************************************
*   Load WP Accessibility Helper TextDomain
**********************************************/
function wp_access_helper_load_plugin_textdomain() {
	$domain = 'wp-accessibility-helper';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) ) {
		return $loaded;
	} else {
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
}
add_action( 'init', 'wp_access_helper_load_plugin_textdomain' );
/*********************************************
*   Register front styles & scripts
**********************************************/
add_action( 'wp_enqueue_scripts', 'wp_access_helper_scripts' );
function wp_access_helper_scripts() {
    wp_register_style( 'wpah-front-styles',  plugin_dir_url( __FILE__ ) . 'assets/css/wp-accessibility-helper.min.css' );
    wp_enqueue_style( 'wpah-front-styles' );
    wp_enqueue_script( 'wp-accessibility-helper', plugin_dir_url( __FILE__ ) . 'assets/js/wp-accessibility-helper.min.js', array('jquery'), '1.0.0', true );
}
/*********************************************
*   Register admin styles
**********************************************/
add_action('admin_head', 'admin_styles');
function admin_styles() {
    wp_register_style( 'wp-accessibility-helper', plugin_dir_url( __FILE__ ).'admin/css/wp-accessibility-helper.css' );
    wp_enqueue_style( 'wp-accessibility-helper' );
    if( is_rtl() ){
        wp_register_style( 'wp-accessibility-helper-rtl', plugin_dir_url( __FILE__ ).'admin/css/wp-accessibility-helper_rtl.css' );
        wp_enqueue_style( 'wp-accessibility-helper-rtl' );
    }
}
/*********************************************
*   Register admin scripts
**********************************************/
function plugin_admin_scripts() {
    wp_enqueue_script( 'jqui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js' );
    wp_enqueue_media();
    wp_enqueue_script( 'admin_colors', plugin_dir_url( __FILE__ ) . 'admin/js/jscolor.min.js' );
    wp_enqueue_script( 'admin_scripts', plugin_dir_url( __FILE__ ) . 'admin/js/admin_scripts.js' );
}
add_action('admin_enqueue_scripts', 'plugin_admin_scripts');
/*********************************************
*   Create WP-Accessibility-Helper HTML Elements
**********************************************/
add_action('wp_footer','wp_access_helper_create_container');
function wp_access_helper_create_container() {
    include_once dirname( __FILE__ ) . '/wp-accessibility-helper-view.php';
    include_once dirname( __FILE__ ) . '/inc/wah-skip-links.php';
}
if( is_admin() ) {
    include_once( dirname(__FILE__)  . '/admin/functions.php');
    include_once( dirname(__FILE__)  . '/admin/ajax-functions.php');
}
/*********************************************
*   Register WAH Skiplinks
**********************************************/
add_action( 'after_setup_theme', 'register_wah_skiplinks_menu' );
function register_wah_skiplinks_menu() {
    register_nav_menu( 'wah_skiplinks', __( 'WAH Skiplinks menu', 'wp-accessibility-helper' ) );
}
