<?php

/**
 * The file that defines the core plugin class
 *
 * @since      1.0.1
 *
 * @package    Post Timeline
 * @subpackage Post_TIMELINE/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.1
 * @package    Post_TIMELINE
 * @subpackage Post_TIMELINE/includes
 * @author     PostLogix <zubair@postlogix.com>
 */
class Post_Timeline {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      Post_TIMELINE_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->plugin_name = 'Post Timeline';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Post_TIMELINE_Loader. Orchestrates the hooks of the plugin.
	 * - Post_TIMELINE_i18n. Defines internationalization functionality.
	 * - Post_TIMELINE_Admin. Defines all hooks for the admin area.
	 * - Post_TIMELINE_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-timeline-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-timeline-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-post-timeline-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-post-timeline-public.php';

		$this->loader = new Post_TIMELINE_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Post_TIMELINE_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Post_TIMELINE_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Post_TIMELINE_Admin( $this->get_plugin_name(), $this->get_version() );

	    $this->loader->add_action( 'init', $plugin_admin, 'register_post_timeline' );
	    $this->loader->add_action( 'init', $plugin_admin, 'register_post_timeline_settings_page' );
	    $this->loader->add_action( 'admin_init', $plugin_admin, 'add_post_timeline_meta_box' );
		$this->loader->add_action( 'after_setup_theme', $plugin_admin, 'include_optiontree' );

	    $this->loader->add_filter( 'ot_header_logo_link', $plugin_admin, 'filter_header_logo_link',100 );
	    $this->loader->add_filter( 'ot_header_version_text', $plugin_admin, 'filter_header_version_text' ,100);
	    $this->loader->add_filter( 'ot_list_item_title_label', $plugin_admin, 'filter_post_list_item_title_label', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Post_TIMELINE_Public( $this->get_plugin_name(), $this->get_version() );

	  $this->loader->add_action( 'init', $plugin_public, 'add_shortcodes' );
	  $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
	  $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	  $this->loader->add_action( 'wp_head', $plugin_public, 'output_header_css' );
	  $this->loader->add_action( 'pre_get_posts', $plugin_public, 'add_timelines_to_loop' );
	  //$this->loader->add_filter( 'the_content', $plugin_public, 'timeline_content_filter' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    Post_TIMELINE_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
