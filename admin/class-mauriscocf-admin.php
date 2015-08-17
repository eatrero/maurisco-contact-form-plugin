<?php
/**
 * Maurisco_Contact_Form_Plugin
 *
 * @package   Maurisco_Contact_Form_Plugin_Admin
 * @author    Ed Atrero <info@mauris.co>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins
 * @copyright 2013 Ed Atrero
 */

/**
 * Maurisco_Contact_Form_Plugin_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * TODO: Rename this class to a proper name for your plugin.
 *
 * @package Maurisco_Contact_Form_Plugin_Admin
 * @author  Ed Atrero <info@mauris.co>
 */
class Maurisco_Contact_Form_Plugin_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * TODO :
		 *
		 * - Decomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = Maurisco_Contact_Form_Plugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
//		add_action( 'TODO', array( $this, 'action_method_name' ) );
//		add_filter( 'TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * TODO :
		 *
		 * - Decomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Maurisco_Contact_Form_Plugin::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Maurisco_Contact_Form_Plugin::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Maurisco Contact Form Plugin Settings', $this->plugin_slug ),
			__( 'Maurisco Contact Form Plugin', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$maurisco_api_id = get_option( 'maurisco_api_id' );
		$maurisco_api_key = get_option( 'maurisco_api_key' );
		$maurisco_style_input_bg_color = get_option( 'maurisco_style_input_bg_color' );
		$maurisco_style_input_width_percentage = get_option( 'maurisco_style_input_width_percentage' );

		$type_arr = maurisco_cf_get_leadtypes();

		foreach ($type_arr as $type){
			error_log('--------------------------');
			error_log(($type->{'name'}));
			error_log(($type->{'description'}));
			error_log(($type->{'_id'}));
			if(property_exists($type , 'fields'))
				error_log(serialize($type->{'fields'}));
		}


		if( isset($_POST['maurisco_api_id']) && isset($_POST['maurisco_api_key']) ) {
			$maurisco_api_id  = $_POST['maurisco_api_id' ];
			$maurisco_api_key = $_POST['maurisco_api_key'];
			$maurisco_style_input_bg_color = $_POST['maurisco_style_input_bg_color'];
			$maurisco_style_input_width_percentage = $_POST['maurisco_style_input_width_percentage'];

			update_option( 'maurisco_api_id',  $maurisco_api_id  );
			update_option( 'maurisco_api_key', $maurisco_api_key );
			update_option( 'maurisco_style_input_bg_color', $maurisco_style_input_bg_color );
			update_option( 'maurisco_style_input_width_percentage', $maurisco_style_input_width_percentage );

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php
		}

		echo 'Use the following short code on any page to include the contact form <pre>[maurisco_cf]</pre>';

		foreach ($type_arr as $type){
				echo 'If you have a specific contact page for ' . $type->{'name'} . " lead types include an attribute in the short code like <pre>[maurisco_cf type='" . strtolower($type->{'name'}). "']</pre>";
		}

		echo '<h2>Settings</h2>';
		echo '<form name="maurisco_admin_form" method="post" action="">';
		echo '<div class="wrap">';
		echo "<div>Enter your Maurisco API ID: <input id='maurisco_api_id' name='maurisco_api_id' size='20' \
			type='text' placeholder='xxxxxxxxxxxxxxxx'  value='" . $maurisco_api_id  . "'></div>";
		echo "<div>Enter your Maurisco API KEY: <input id='maurisco_api_key' name='maurisco_api_key' size='40' \
			type='text' placeholder='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' value='" . $maurisco_api_key . "'></div>";
		echo '</div>';
		echo "<hr>";
		echo "<h2>Styling</h2>";
		echo "<div>Input Textbox Background (RGB Hex):<input id='maurisco_style_input_bg_color' \
		 name='maurisco_style_input_bg_color' size='40' \
		 type='text' placeholder='FFFFFF' value='" . $maurisco_style_input_bg_color . "'></div>";
		echo "<div>Input Textbox Width (%):<input id='maurisco_style_input_width_percentage' \
		 name='maurisco_style_input_width_percentage' size='40' \
		 type='text' placeholder='100' value='" . $maurisco_style_input_width_percentage . "'></div>";
		echo "<hr>";
		echo "<input type='submit' name='Save' class='button-primary' value='Save' />";
		echo '</form>';
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}
