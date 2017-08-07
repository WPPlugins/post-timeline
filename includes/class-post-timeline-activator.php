<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    Post_TIMELINE
 * @subpackage Post_TIMELINE/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    Post_TIMELINE
 * @subpackage Post_TIMELINE/includes
 * @author     PostLogix <zubair@postlogix.com>
 */
class Post_TIMELINE_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {

		global $wpdb;
		$charset_collate = 'utf8';
		$prefix 	 	 = $wpdb->prefix."";

		$wpdb->query("UPDATE `{$prefix}posts` SET post_type = 'post-timeline' WHERE post_type = 'agile-timeline'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-post-date' WHERE meta_key = 'agile-timeline-post-date'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-date-format' WHERE meta_key = 'agile-timeline-date-format'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-post-color' WHERE meta_key = 'agile-timeline-post-color'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-image-overlay' WHERE meta_key = 'agile-timeline-image-overlay'");

	}

}
