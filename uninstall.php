<?php
/*
 * Uninstall plugin
 */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

if( !WP_UNINSTALL_PLUGIN ){
	exit();
}

//TODO: Tabellen löschen