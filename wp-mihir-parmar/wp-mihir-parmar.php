<?php
/*
Plugin Name: WP MIHIR PARMAR
Description: ADD COUPAN
Author: mihir parmar
Version: 1.0
Author URI: https://example.com/
Text Domain : wp-mihir-parmar
*/
if (!defined('ABSPATH')) exit;

 
define( 'WPC_VERSION', '1.0' );
if (!defined("WPC_PLUGIN_DIR_PATH"))
	define("WPC_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));

if (!defined("WPC_PLUGIN_URL"))
    define("WPC_PLUGIN_URL", plugins_url() . '/' . basename(dirname(__FILE__)));
    

$wmp_plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$wmp_plugin", 'wmp_plugin_add_settings_link');

function wmp_plugin_add_settings_link($wmp_links){

    $wpc_settings_link = '<a href="admin.php?page=wpc-listing-coupons">' . __( 'Settings', 'wp-mihir-parmar' ) . '</a>';
	array_unshift( $wmp_links , $wpc_settings_link );
	
	return $wmp_links;

}

function wps_options_install() {

    global $wpdb;   
    $table_name = $wpdb->prefix . 'coupons_mihir_parmar';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        title varchar(100) NOT NULL,
        description text NOT NULL,
        amount int(20) DEFAULT NULL,
        image varchar(255) NOT NULL,
        category varchar(20) NOT NULL,
        availability varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

}
register_activation_hook(__FILE__, 'wps_options_install');


add_action( 'admin_enqueue_scripts', 'wpc_enqueue_styles_scripts' );
function wpc_enqueue_styles_scripts(){
    if( is_admin() ) { 

        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
        wp_enqueue_style( 'main-wpc-css', WPC_PLUGIN_URL . "/assets/css/style.css", '',WPC_VERSION);
        $wpc_js = WPC_PLUGIN_URL . "/assets/js/custom-admin-script.js";       
        wp_enqueue_script( 'wsppcp-custom-js', $wpc_js, array('jquery'), WPC_VERSION, true );
    }
    
}

function wpc_get_records($wpc_title)
{   
    global $wpdb;
    $table_name = $wpdb->prefix . 'coupons_mihir_parmar';
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE title = %s",$wpc_title);    
    $results = $wpdb->get_results($query);
    $num_rows = count($results);
    return $num_rows;
}

require_once(WPC_PLUGIN_DIR_PATH . 'admin/settings.php');
