<?php
/**
 * Plugin Name: WP Instagram Post
 * Plugin URI: http://genialsouls.com/
 * Description: This plugin will upload/post your posts as well as Woo products, to Instagram on publish.
 * Author: Qamar Sheeraz
 * Author URI: https://profiles.wordpress.org/qsheeraz
 * Version: 1.0
 * Stable tag: 1.0
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 require_once( 'classes/class-woo-igp.php' );
 
 // Instagram integrations.
 require_once( 'vendor/autoload.php' );

 global $wpigp;
 $wpigp = new Woo_IGP( __FILE__ );
 $wpigp->version = '1.0';
 $wpigp->init();
?>