<?php

/**
 * Plugin Name:       Post Timeline
 * Plugin URI:        https://posttimeline.com/
 * Description:       Post Timeline is the Great WordPress Timeline Plugin, through which users can create unlimited beautiful Vertical timelines.
 * Version:           2.0.0
 * Author:            AgileLogix
 * Author URI:        http://posttimeline.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       post-timeline
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'POST_TIMELINE_URL_PATH', plugin_dir_url( __FILE__ ) );
define( 'POST_TIMELINE_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'POST_TIMELINE_PREFIX', $wpdb->prefix."atl_" );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_post_timeline() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-timeline-activator.php';
	Post_TIMELINE_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_post_timeline() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-timeline-deactivator.php';
	Post_TIMELINE_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_post_timeline' );
register_deactivation_hook( __FILE__, 'deactivate_post_timeline' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require plugin_dir_path( __FILE__ ) . 'includes/class-post-timeline.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */

function run_post_timeline() {

	//main update method
	$plugin = new Post_Timeline();
	$plugin->run();

}
run_post_timeline();
