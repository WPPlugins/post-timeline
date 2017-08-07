<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Post_TIMELINE
 * @subpackage Post_TIMELINE/admin
 * @author     PostLogix <zubair@postlogix.com>
 */
class Post_TIMELINE_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


			if ( !session_id() ) {
			    session_start();
			}

		//add_action('admin_notices', array(&$this,'my_admin_notice'));		
		//add_action('save_post', array(&$this,'validate_text_input'));
		//add_filter('ot_validate_setting', array(&$this,'validate_text_input'), 10, 3);

		add_filter('manage_edit-post-timeline_columns',array(&$this,'timeline_columns'));
		add_action( 'manage_post-timeline_posts_custom_column' , array(&$this,'timeline_column_details'), 10, 2 );

		
	}



	function validate_text_input($post_id) {
		global $errors;

		if($_POST['post-timeline-post-date'] == '') {

			set_transient( 'post-timeline-err', 'Please add Post Date for the Timeline', 30 );

			return false;
		}

	    return true;
	}


	function my_admin_notice() {

		if ( $error = get_transient( "post-timeline-err" ) ) { ?>
		    <div class="error">
		        <p><?php echo $error; ?></p>
		    </div><?php

		    delete_transient("post-timeline-err");
		}
		
	}

	/**
	 * Register Timeline custom post type
	 *
	 * @since    0.0.1
	 */
	public function register_post_timeline() {


	    $labels = array(
	      'name'               => _x( 'Post Timelines', 'post type general name', 'post-timeline' ),
	      'singular_name'      => _x( 'Post Timeline', 'post type singular name', 'post-timeline' ),
	      'menu_name'          => _x( 'Post Timelines', 'admin menu', 'post-timeline' ),
	      'name_admin_bar'     => _x( 'Post Timeline', 'add new on admin bar', 'post-timeline' ),
	      'add_new'            => _x( 'Add New', 'timeline', 'post-timeline' ),
	      'add_new_item'       => __( 'Add New Timeline', 'post-timeline' ),
	      'new_item'           => __( 'New Timeline', 'post-timeline' ),
	      'edit_item'          => __( 'Edit Timeline', 'post-timeline' ),
	      'view_item'          => __( 'View Timeline', 'post-timeline' ),
	      'all_items'          => __( 'All Timelines', 'post-timeline' ),
	      'search_items'       => __( 'Search Timelines', 'post-timeline' ),
	      'parent_item_colon'  => __( 'Parent Timelines:', 'post-timeline' ),
	      'not_found'          => __( 'No timelines found.', 'post-timeline' ),
	      'not_found_in_trash' => __( 'No timelines found in Trash.', 'post-timeline' ),
	      "parent"  => __( 'Parent Timeline', 'post-timeline' ),
	    );

	    $args = array(
	      'labels'            => $labels,
	      'public'            => true,
	      'publicly_queryable'=> true,
	      'show_ui'           => true,
	      'show_in_menu'      => true,
	      'query_var'         => true,
	      'rewrite'           => array( 'slug' => 'post-timeline' ),
	      'capability_type'   => 'post',
	      'has_archive'       => true,
	      'hierarchical'      => true,
	      'menu_position'     => 5,
	      'menu_icon'         => 'dashicons-portfolio',
	      'supports'          => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'page-attributes'),
	    );

	  	register_post_type( 'post-timeline', $args );

	  	add_filter( 'template_include', array($this,'include_template_function'), 1 );

	}

	function include_template_function( $template_path ) {
	 
	    if ( get_post_type() == 'post-timeline' ) {

	        if ( is_single() ) {
	            // checks if the file exists in the theme first,
	            // otherwise serve the file from the plugin
	            if ( $theme_file = locate_template( array ( 'single-post-timeline.php' ) ) ) {
	                $template_path = $theme_file;
	            } else {
	                $template_path = POST_TIMELINE_PLUGIN_PATH . 'public/partials/post-timeline-page.php';
	            }
	        }
	    }
	    return $template_path;
	}

	/**
	 * Initialize the timeline settings meta box.
	 *
	 * @since    0.0.1
	 */
	function add_post_timeline_meta_box() {

		$settings = get_option( 'post_timeline_global_settings' );


	    $timeline_meta_box = array(
	      'id'        => 'post_timeline_timeline_metabox',
	      'title'     => __( 'Timeline Details', 'post-timeline' ),
	      'desc'      => '',
	      'pages'     => array( 'post-timeline' ),
	      'context'   => 'normal',
	      'priority'  => 'high',
	      'fields'    => array(
	        array(

	        	'id'          => 'post-timeline-post-date',
			    'label'       => __( 'Date Picker', 'post-timeline' ),
			    'desc'        => __( 'Select the Date/Month/Year of Timeline', 'post-timeline' ),
			    'type'        => 'date-picker',
			    'value'		  => '0'
	        ),
			array(
		        'id'          => 'post-timeline-date-format',
		        'label'       => __( 'Date Format', 'post-timeline' ),
		        'desc'        => __( 'Select the Date format to appear, if event day or month is unkown select year.', 'post-timeline' ),
		        'type'        => 'select',
	          	'choices'     => array(
			        array(
			            'label'     => __( 'Full Date', 'post-timeline' ),
			            'value'     => '0'
			        ),
		            array(
		              'label'     => __( 'Month Only', 'post-timeline' ),
		              'value'     => '1'
		            ),
		            array(
		              'label'     => __( 'Year Only', 'post-timeline' ),
		              'value'     => '2'
		            )
		        )
	        ),
	        array(
		        'id'          => 'post-timeline-img-txt-pos',
		        'label'       => __( 'Position', 'post-timeline' ),
		        'desc'        => __( 'Select the Position of Text and Image.', 'post-timeline' ),
		        'type'        => 'select',
	          	'choices'     => array(
			        array(
			            'label'     => __( 'Image Top', 'post-timeline' ),
			            'value'     => '0'
			        ),
		            array(
		              'label'     => __( 'Text Top', 'post-timeline' ),
		              'value'     => '1'
		            )
		        )
	        ),
	        array(
		        'id'          => 'post-timeline-image-overlay',
		        'label'       => __( 'Sub Heading or Caption', 'post-timeline' ),
		        'desc'        => __( 'Sub Heading for Parent Post & Caption for Child Posts', 'post-timeline' ),
		        'type'        => 'text',
	        ),
	       	array(
		        'id'          => 'post-timeline-post-color',
		        'label'       => __( 'Post Color', 'post-timeline' ),
		        'desc'        => __( 'Use When Creating Child Post.', 'post-timeline' ),
		        'type'        => 'select',
	          	'choices'     => array(
			        array(
			            'label'     => __( 'Color 1', 'post-timeline' ),
			            'value'     => '0'
			        ),
		            array(
			            'label'     => __( 'Color 2', 'post-timeline' ),
			            'value'     => '1'
			        ),
			        array(
			            'label'     => __( 'Color 3', 'post-timeline' ),
			            'value'     => '2'
			        ),
			        array(
			            'label'     => __( 'Color 4', 'post-timeline' ),
			            'value'     => '3'
			        ),
			        array(
			            'label'     => __( 'Color 5', 'post-timeline' ),
			            'value'     => '4'
			        ),
			        array(
			            'label'     => __( 'Color 6', 'post-timeline' ),
			            'value'     => '5'
			        ),
			        array(
			            'label'     => __( 'Color 7', 'post-timeline' ),
			            'value'     => '6'
			        )
		        )
	        ),
	      )
	    );

	    ot_register_meta_box( $timeline_meta_box );

	}

	function timeline_columns($gallery_columns) {
		

		return array(
				"cb"  			=>  '<input type="checkbox" />',
				"title"  		=>  _x('Timeline Title', 'post-timeline'),
				"images"  		=>  __('Timeline Image'),
				"event_date"  	=>  __('Event Date'),
				"date"  		=>  _x('Published', 'post-timeline'),
				"content"  		=>  _x('Timeline Content', 'post-timeline')
			);
	}



	function timeline_column_details( $_column_name, $_post_id ) {
		

		switch ( $_column_name ) {
			
			case "event_date":

				
				$timeline_date = get_post_meta( $_post_id, 'post-timeline-post-date', true );
				

				if($timeline_date ) {

					$timeline_date = date_format(date_create($timeline_date)," M - d - Y");
				}

				echo $timeline_date;
				
				break;

			case "images":

				$post_image_id = get_post_thumbnail_id(get_the_ID());
				
				if ($post_image_id) {
					
					$thumbnail = wp_get_attachment_image_src( $post_image_id, array(150,150), false);
					if ($thumbnail) (string)$thumbnail = $thumbnail[0];
					echo '<img src="'.$thumbnail.'" alt="" />';
				}
			  	break;

			case "content":
				echo  $content = get_the_excerpt();
				break;
		  }
	}

	/**
	 * Filter the required "title" field for list-item option types.
	 *
	 * @since    0.0.1
	 */
  	function filter_post_list_item_title_label( $label, $id ) {

	    if ( $id == 'post-timeline-timeline-yarns' ) {
	      $label = __( 'Yarn name', 'post-timeline' );
	    }

	    if ( $id == 'post-timeline-timeline-tools' ) {
	      $label = __( 'Size', 'post-timeline' );
	    }

	    if ( $id == 'post-timeline-timeline-notions' ) {
	      $label = __( 'Notion', 'post-timeline' );
	    }

	    return $label;

  	}

	//TODO Next two functions are terribad

	/**
	 * Filter the OptionTree header logo link
	 *
	 * @since    0.0.1
	 */
  	function filter_header_logo_link() {

		$screen = get_current_screen();
		if( $screen->id == 'page_post_timeline-settings' ) {
			return '';
		} else {
			return '<a href="http://wordpress.org/extend/post-timeline/" target="_blank">Post Timeline</a>';
		}

  	}

	/**
	 * Filter the OptionTree header version text
	 *
	 * @since    0.0.1
	 */
	function filter_header_version_text() {

		$screen = get_current_screen();
		if( $screen->id == 'page_post_timeline-settings' ) {
			return '<a href="http://wordpress.org/plugins/post-timeline" target="_blank">' . $this->plugin_name . ' - v' . $this->version . '</a>';
		} else {
			return 'POST Timeline';
		}

	}


	/**
	 * OptionTree options framework for generating plugin settings page & metaboxes.
	 *
	 * Only needs to load if no other theme/plugin already loaded it.
	 *
	 * @since 0.0.1
	 */
	function include_optiontree() {

		if ( ! class_exists( 'OT_Loader' ) ) {
    	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/option-tree/ot-loader.php';

			/* TODO - probably shouldn't be doing this here */
			add_filter( 'ot_show_pages', '__return_false' );
			add_filter( 'ot_use_theme_options', '__return_false' );
		}

	}

	/**
	 * Registers a new global timeline settings page.
	 *
	 * @since    0.0.1
	 */
	public function register_post_timeline_settings_page() {

		// Only execute in admin & if OT is installed
	  	if ( is_admin() && function_exists( 'ot_register_settings' ) ) {


		    // Register the page
	    	ot_register_settings(
	        array(
	      		array(
	            'id'              => 'post_timeline_global_settings',
	            'pages'           => array(
	              array(
		              'id'              => 'post-timeline-settings',
		              'parent_slug'     => 'edit.php?post_type=post-timeline',
		              'page_title'      => __( 'Post Timeline - Global Settings', 'post-timeline' ),
		              'menu_title'      => __( 'Settings', 'post-timeline' ),
		              'capability'      => 'edit_theme_options',
		              'menu_slug'       => 'post-timeline-settings',
		              'icon_url'        => null,
		              'position'        => null,
		              'updated_message' => __( 'Settings updated', 'post-timeline' ),
		              'reset_message'   => __( 'Settings reset', 'post-timeline' ),
		              'button_text'     => __( 'Save changes', 'post-timeline' ),
		              'show_buttons'    => true,
		              'screen_icon'     => 'options-general',
		              'contextual_help' => null,
		              'sections'        => array(
		                array(
		                  'id'          => 'post-timeline-general',
		                  'title'       => __( 'General', 'post-timeline' ),
		                )
		              ),
	                'settings'        => array(
							array(
								'id'        => 'post-timeline-custom-css',
								'label'     => __( 'Custom CSS', 'post-timeline' ),
								'desc'      => __( 'Add your css for the timeline colors', 'post-timeline' ),
								'type'      => 'css',
								'section'   => 'post-timeline-general',
							)
	                )
	              )
	            )
	          )
	        
	        ));

		}

	}

}
