<?php
/**
 * Maurisco_Contact_Form_Plugin
 *
 * @package   Maurisco_Contact_Form_Plugin
 * @author    Ed Atrero <info@mauris.co>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins
 * @copyright 2013 Ed Atrero
 */

/**
 * Maurisco_Contact_Form_Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-mauriscocf-admin.php`
 *
 *
 * @package Maurisco_Contact_Form_Plugin
 * @author  Ed Atrero <info@mauris.co>
 */
class Maurisco_Contact_Form_Plugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '0.0.1';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'mauriscocf';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
//		add_action( 'TODO', array( $this, 'action_method_name' ) );
//		add_filter( 'TODO', array( $this, 'filter_method_name' ) );
		add_action('wp_head', array( $this, 'hook_css' ) );

		add_shortcode( 'maurisco_cf', array( $this, 'maurisco_cf_sc') );
		add_action('wp_print_footer_scripts', array( $this, 'hook_localize_scripts' ) );

	}

	/**
	 * Return the markup for each field.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	private function field_parser( $field ) {
		$req = $field->{'required'} ? " required " : " ";
		$label = $field->{'description'}  ? $field->{'description'} : "";

		$out  = "<div class='maurisco_cf_input_group row'>";
		$out .= "<div class='twelve columns'>";
		$out .= "<label class=''>" . $label . "</label>";
		switch ( $field->{'name'} ) {
			case 'name_0' :
			case 'name_1' :
			case 'name_2' :
			case 'event_location_1' :
			case 'question_1' :
			case 'question_2' :
			case 'phone' :
				$out .= "<input id='maurisco_cf_" . $field->{'name'} . "' name='" . $field->{'name'} . "' class='maurisco_cf_input u-full-width maurisco_cf_text' type='text'" . $req .  "autofocus placeholder='" . $field->{'placeholder'} . "'/>";
				break;

			case 'event_date' :
				$out .= "<input id='maurisco_cf_" . $field->{'name'} . "' name='" . $field->{'name'} . "' class='maurisco_cf_input u-full-width maurisco_cf_date' type='text'" . $req .  " placeholder='" . $field->{'placeholder'} . "' size='20'/>";
				break;

			case 'email_0' :
			case 'email_1' :
				$out .= "<input id='maurisco_cf_" . $field->{'name'} . "' name='" . $field->{'name'} . "' class='maurisco_cf_input u-full-width maurisco_cf_email' type='email'" . $req .  "autofocus placeholder='" . $field->{'placeholder'} . "' />";
				break;

			case 'comment_1' :
				$out .= "<textarea id='maurisco_cf_" . $field->{'name'} . "' name='" . $field->{'name'} . "' class='maurisco_cf_input u-full-width maurisco_cf_text_area'" . $req .  "autofocus rows='10' cols='50' placeholder='" . $field->{'placeholder'} . "'></textarea>";
				break;

		}
		$out .= "</div>";
		$out .= "</div>";

		return $out;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . 'languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-jquery-ui', plugins_url( 'assets/css/jquery-ui.min.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style( $this->plugin_slug . '-public-css', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style( $this->plugin_slug . '-normalize-css', plugins_url( 'assets/css/normalize.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		error_log('enqueue_scripts');
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'maurisco-cf-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
		wp_enqueue_script( 'jquery-validator', '//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.js', array( 'jquery' ), self::VERSION, true);
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

	}

	function hook_localize_scripts() {
		error_log('hook_localize_scripts');
		error_log(admin_url('admin-ajax.php'));
/*
		wp_localize_script(
			'maursico-cf-plugin-script',
			'maurisco_cf_form',
			array(
				'ajax_url' =>  admin_url( 'admin-ajax.php' ),
				'nonce'    =>  wp_create_nonce( 'return_posts' )
			)
		);
*/
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

	/**
	 * maurisco_cf_sc handle shortcode generation
	 *
	 * @since    1.0.0
	 */
	public function maurisco_cf_sc( $atts ) {

		$a = shortcode_atts( array(
			'type' => ''
		), $atts );
		$maurisco_api_id = get_option( 'maurisco_api_id');
		$type_arr = maurisco_cf_get_leadtypes();

		$match = 0;

		if($atts['type'] && is_array($type_arr)){
			foreach ($type_arr as $type ) {
				error_log('looking at ' . $atts['type'] . ' '. $type->{'name'});
				if( !strcasecmp( $atts['type'], $type->{'name'} ) ){
					$match = $type;
					error_log('found match');
				}
			}
		}

		$output  = "<div><form id='maurisco_cf' class='maurisco_cf_form' name='maurisco_cf'>";
		$output .= "<fieldset>";
		$output .= "<div><input id='maurisco_cf_nonce' type='hidden' value='" . wp_create_nonce( 'return_posts' ) . "' /></div>";
		$output .= "<div><input id='maurisco_id' type='hidden' value='" . md5($maurisco_api_id) . "' /></div>";
		$output .= "<div><input id='maurisco_cf_url' type='hidden' value='" . admin_url( 'admin-ajax.php' ) . "' /></div>";

		if($match){
			$output .= "<div><input id='maurisco_cf_event_type' type='hidden' value='" . $match->{'name'} . "'/></div>";
			if(is_array($type_arr)){
				foreach ($match->{'fields'} as $field){
					$field_markup = $this->field_parser( $field );
					error_log($field_markup);
					if( $field_markup )
						$output .= $field_markup;
				}
			} else {
				error_log('ERROR : not field types found');
			}

		} else {

			$output .= "<div><input id='maurisco_cf_first_name' class='maurisco_cf_input u-full-width maurisco_cf_text' type='text' required autofocus placeholder='First Name'/></div>";
			$output .= "<div><input id='maurisco_cf_last_name' class='maurisco_cf_input u-full-width maurisco_cf_text' type='text' required autofocus placeholder='Last Name'/></div>";
			$output .= "<div><input id='maurisco_cf_email' class='maurisco_cf_input u-full-width maurisco_cf_email' type='email' required placeholder='Email'/></div>";
			$output .= "<div><input id='maurisco_cf_date' class='maurisco_cf_input u-full-width maurisco_cf_date' type='text' required placeholder='Date' size='20'/></div>";

			if(is_array($type_arr)){
				$output .= "<div><select id='maurisco_cf_event_type' class='maurisco_cf_select' name='event_type'>";
				foreach ($type_arr as $type){
					$output .= "  <option value='" . $type->{'name'} . "'>" . ($type->{'name'}) ."</option>";
				}
				$output .= "</select></div>";
			}
			$output .= "<div><input id='maurisco_cf_location' class='maurisco_cf_input u-full-width maurisco_cf_text' type='text' required placeholder='Event Location'/></div>";
			$output .= "<div><textarea id='maurisco_cf_comments1' class='maurisco_cf_input u-full-width maurisco_cf_text_area' required placeholder='Comments or questions?' rows='10' cols='50'></textarea></div>";
		}

		$output .= "<div><button id='maurisco_cf_submit' type='submit' class='maurisco_cf_button'>Submit</button></div>";
		$output .= "</fieldset>";
		$output .= "</form></div>";
		$output .= "<div id='maurisco_cf_message' class='maurisco-cf-message'></div>";

	  return $output;

	}

	/**
	 * maurisco_cf_sc add styling hook
	 *
	 * @since    1.0.0
	 */
	public function hook_css()
	{
		$maurisco_style_input_bg_color = get_option( 'maurisco_style_input_bg_color' );
		if(!$maurisco_style_input_bg_color)
			$maurisco_style_input_bg_color = '#f6f6f6';
		else
			$maurisco_style_input_bg_color = '#'. $maurisco_style_input_bg_color;

		$maurisco_style_input_width_percentage = get_option( 'maurisco_style_input_width_percentage' );
		if(!$maurisco_style_input_width_percentage)
			$maurisco_style_input_width_percentage = '100';

		$output = "<style>
		.maurisco_cf_input {
			display: block;
			width: ". $maurisco_style_input_width_percentage ."%;
			padding: 6px 12px;
			font-size: 14px;
			line-height: 1.42857143;
			color: #555555;
			background-color: ". $maurisco_style_input_bg_color .";
			background-image: none;
			border: 0px solid #676767;
			border-radius: 0px;
			margin-top: 10px;
			margin-bottom:10px;
		}

		.maurisco_cf_textarea {
			height: 30px;
		}

		.maurisco_cf_textarea {
			height: 100px;
		}
		.maurisco_cf_button {
			background-color: rgb(192,192,192);
		}
		</style>";

		echo $output;
	}

}
