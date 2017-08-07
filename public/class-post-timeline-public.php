<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Post_TIMELINE
 * @subpackage Post_TIMELINE/public
 * @author     PostLogix <zubair@postlogix.com>
 */
class Post_TIMELINE_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name.'-bootstrap', POST_TIMELINE_URL_PATH . 'public/css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-bootstrap-theme', POST_TIMELINE_URL_PATH . 'public/css/bootstrap-theme.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-timeline', POST_TIMELINE_URL_PATH . 'public/css/post-timeline-public.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {


	}

	/**
	 * Register individual shortcode [post_timeline]
	 *
	 * @since    0.0.1
	 */
  	public function add_shortcodes() {
  		
  		add_shortcode( 'post-timeline', array( $this, 'timeline_shortcode' ) );
  	}

	/**
	 * Callback function for individual pattern shortcode [post-timeline]
	 *
	 * @since    0.0.1
	 */
  	public function timeline_shortcode( $atts ) {
		

		extract( shortcode_atts(array(
			'id' => '',
		), $atts, 'post-timeline' ) );

		//If no ID is set, do nothing.
		if( $id == '') {
			return;
		}

		$post = get_post( $id );

		//If no matching pattern found, do nothing.
		if( !$post || $post->post_type != 'post-timeline' ) {
			return;
		}

		//ID is valid, show pattern.
		return $this->output_timeline( $post );
  	}


	/**
	 * Output a pattern
	 *
	 * @since    0.0.1
	 */
	public function output_timeline( $parent_post ) {

		$args = array(
			'post_parent' => $parent_post->ID,
			'post_type'   => 'post-timeline', 
			'numberposts' => -1,
			'post_status' => 'publish'
		);


		$child_posts 		 = get_children( $args );
		$parent_post->custom = get_post_custom($parent_post->ID);
		

		/*
		the_post_thumbnail_url( 'thumbnail' );       // Thumbnail (default 150px x 150px max)
		wp_get_attachment_image_url( 'medium' );          // Medium resolution (default 300px x 300px max)
		the_post_thumbnail_url( 'large' );           // Large resolution (default 640px x 640px max)
		the_post_thumbnail_url( 'full' );  
		*/
		$child_posts = get_children( $args );
		foreach($child_posts as $child) {

			$child->custom 		= get_post_custom($child->ID);
			$date 				= $child->custom['post-timeline-post-date'][0];
			$format 			= $child->custom['post-timeline-date-format'][0];
			
			//$date = null;
			if(!$date) {

				$date = $child->post_date;
			}

			$child->event_date  = $date;

			if($date) {

				//$child->date_str 	= date_format(date_create($child->custom['post-timeline-post-date'][0]),"d M");
				switch ($format) {
					case '0':
						
						$child->date_str 	= date_i18n("d M",strtotime($child->custom['post-timeline-post-date'][0]));
						break;
					
					case '1':
						
						$child->date_str 	= date_i18n("M",strtotime($child->custom['post-timeline-post-date'][0]));	
						break;


					case '2':
						
						$child->date_str 	= date_i18n("Y",strtotime($child->custom['post-timeline-post-date'][0]));	
						break;

					default:
						$child->date_str 	= date_i18n("d M",strtotime($child->custom['post-timeline-post-date'][0]));	
						break;
				}
			}
			else
				$child->date_str = '';
		}

		
		//dd($child_posts);

		//dd(wp_get_attachment_image_url(1165,'large'));

		//Sort By Year
		usort($child_posts, array($this, "cmp"));


		
		//date to and from
		if(isset($child_posts[0]) && $child_posts[0]->event_date) {

			$_comp = explode('-', $child_posts[0]->event_date);
			$parent_post->time_range = $_comp[0];
		}

		$last_post = $child_posts[count($child_posts) - 1];
		if(isset($last_post) && $last_post->event_date) {

			$_comp = explode('-', $last_post->event_date);
			$parent_post->time_range .= ' - '.$_comp[0];
		}


		//Split Date Year
		foreach($child_posts as $child) {

			$date = $child->event_date;
			
			if($date) {
				$date_comp = explode('-', $date);
				$child->date_comp = $date_comp;
			}
		}


		
		ob_start();
		include( plugin_dir_path( __FILE__ ) . '/partials/post-timeline-public-display.php' );
		$output = ob_get_contents();
		ob_end_clean();


		return $output;

	}

	private function cmp($a, $b){
    	
    	return strcmp($a->event_date, $b->event_date);
	}

  	/**
	 * Include patterns in loop so they can be displayed among regular posts.
	 *
	 * @since    0.0.1
	 */
	function add_timelines_to_loop( $query ) {

		global $pagenow;

		if( $pagenow == 'edit.php' ) {
			
			return;
		}

		// Querying specific page (not set as home/posts page) or attachment
		if( !$query->is_home() ) {
			
			if( $query->is_page() || $query->is_attachment() ) {
				return;
			}
		}

		// Querying a specific taxonomy
		if( !is_null( $query->tax_query ) ) {
			
			$tax_queries = $query->tax_query->queries;
			$pattern_taxonomies = get_object_taxonomies( 'pattern' );

			if( is_array($tax_queries) ) {
			
				foreach( $tax_queries as $tax_query ) {
			
					  	if( isset( $tax_query['taxonomy'] ) && $tax_query['taxonomy'] !== '' && !in_array( $tax_query['taxonomy'], $pattern_taxonomies ) ) {
				
						  	return;
					  	}
					}
				}
			}

		$post_type = $query->get( 'post_type' );

		if( $post_type == '' || $post_type == 'post' ) {
			$post_type = array( 'post','pattern' );
		}
		else if( is_array($post_type) ) {
			
			if( in_array('post', $post_type) && !in_array('pattern', $post_type) ) {
				
				$post_type[] = 'pattern';
			}
		}

		$post_type = apply_filters( 'post_timeline_query_posts', $post_type, $query );

		$query->set( 'post_type', $post_type );

		return;

	}

  	/**
	 * Adds custom CSS from global settings page to site header.
	 *
	 * @since    0.0.1
	 */
	public function output_header_css() {

		$settings = get_option( 'post_timeline_global_settings' );

		if( $settings['post-timeline-custom-css'] ) {
			
			$css = '<style type="text/css">' . $settings['post-timeline-custom-css'] . '</style>';
			echo $css;
		}

	}

}
