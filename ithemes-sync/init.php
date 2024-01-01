<?php
/*
Plugin Name: Solid Central
Plugin URI: http://solidwp.com/central
Description: Maximize and amplify your admin with remote, multi-site management. One centralized dashboard to save time.
Author: SolidWP
Version: 3.0.1
Author URI: http://solidwp.com/
Domain Path: /lang/
iThemes Package: ithemes-sync
*/

if ( ! empty( $GLOBALS['ithemes_sync_path'] ) ) {
	$active_plugin_path = preg_replace( '|^' . preg_quote( ABSPATH, '|' ) . '|', '', $GLOBALS['ithemes_sync_path'] );
	$this_plugin_path = preg_replace( '|^' . preg_quote( ABSPATH, '|' ) . '|', '', dirname( __FILE__ ) );

	if ( $active_plugin_path != $this_plugin_path ) {

		add_action( 'all_admin_notices', function(){
			echo '<div class="error"><p>';
			echo sprintf( __( 'Only one Solid Central plugin can be active at a time. The plugin at <code>%1$s</code> is running while the plugin at <code>%2$s</code> was skipped in order to prevent errors. Please deactivate the plugin that you do not wish to use.', 'it-l10n-ithemes-sync' ), $active_plugin_path, $this_plugin_path );
			echo '</p></div>';
		}, 0 );
	}

	return;
}

// Show warning PHP versions < 5.6
if ( PHP_VERSION_ID < 50600 ) {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-error"><p>';
		echo __( 'Solid Central requires PHP 5.6 or greater. Please update you PHP version to ensure all features work properly.', 'it-l10n-ithemes-sync' );
		echo '</p></div>';
	}, 0 );
}

$GLOBALS['ithemes_sync_path'] = dirname( __FILE__ );

require( $GLOBALS['ithemes_sync_path'] . '/load.php' );

/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 *
 * @since 1.12.0
 */
function ithemes_sync_activation() {
    if ( ! wp_next_scheduled ( 'ithemes_sync_daily_schedule' ) ) {
	    wp_schedule_event( strtotime( 'Tomorrow 2AM' ), 'daily', 'ithemes_sync_daily_schedule' );
	}
}
register_activation_hook( __FILE__, 'ithemes_sync_activation' );

/**
 * On deactivation, remove all functions from the scheduled action hook.
 *
 * @since 1.12.0
 */
function ithemes_sync_deactivation() {
    wp_clear_scheduled_hook( 'ithemes_sync_daily_schedule' );
}
register_deactivation_hook( __FILE__, 'ithemes_sync_deactivation' );
