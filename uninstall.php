<?php
/*
 * Uninstall plugin
 */

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

require_once __FILE__ . 'includes/class-iup-database-manager.php';

delete_option( 'ionic_user_push' );

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}" . Ionic_User_Database_Manager::USER_ID_TABLE_NAME );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}" . Ionic_User_Database_Manager::LOG_TABLE_NAME );