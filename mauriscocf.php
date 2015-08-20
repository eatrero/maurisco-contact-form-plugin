<?php
/**
 * Maurisco Contact Form Plugin
 *
 * A contact form plugin for Maurisco App users
 *
 * @package   Maurisco_Contact_Form_Plugin
 * @author    Ed @ Maurisco <info@mauris.co>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins
 * @copyright 2015 Maurisco
 *
 * @wordpress-plugin
 * Plugin Name:       Maurisco Contact Form Plugin
 * Plugin URI:        http://wordpress.org/plugins
 * Description:       A contact form plugin for Maurisco App users
 * Version:           0.0.1
 * Author:            Maurisco
 * Author URI:        http://mauris.co
 * Text Domain:       mauriscocf
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: mauriscocf
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/class-mauriscocf.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/index.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Maurisco_Contact_Form_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Maurisco_Contact_Form_Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Maurisco_Contact_Form_Plugin', 'get_instance' ) );

//First we add the actions hooks
add_action('wp_ajax_maurisco_cf_plugin', 'maurisco_cf_plugin_callback' );
add_action('wp_ajax_nopriv_maurisco_cf_plugin', 'maurisco_cf_plugin_callback' );

function maurisco_cf_plugin_callback() {
//	error_log('maurisco_cf_plugin_callback 1');

//    $nonce = $_POST['maurisco_cf_nonce'];
    // The first thing we do is check the nonce and kill the script if wrong
//    if ( ! wp_verify_nonce( $nonce, 'return_posts' ) ){
//        die ( 'Wrong nonce!');
//    }

//    error_log($nonce);
    error_log(serialize($_POST));

	$name_0 = $_POST['name_0'] ? $_POST['name_0'] : null;
	$email_0 = $_POST['email_0'] ? $_POST['email_0'] : null;
	$name_1 = $_POST['name_1'] ? $_POST['name_1'] : null;
	$email_1 = $_POST['email_1'] ? $_POST['email_1'] : null;
	$name_2 = $_POST['name_2'] ? $_POST['name_2'] : null;
	$email_2 = $_POST['email_2'] ? $_POST['email_2'] : null;

	$phone = $_POST['phone'] ? $_POST['phone'] : null;

	$event_date = $_POST['event_date'] ? $_POST['event_date'] : null;

	$event_location_1 = $_POST['event_location_1'] ? $_POST['event_location_1'] : null;
	$event_type = $_POST['event_type'] ? $_POST['event_type'] : null;

	$question_1 = $_POST['question_1'] ? $_POST['question_1'] : null;
	$question_2 = $_POST['question_2'] ? $_POST['question_2'] : null;
	$comment_1 = $_POST['comment_1'] ? $_POST['comment_1'] : null;

	$userIp = $_POST['userIp'];

	$maurisco_api_id = get_option( 'maurisco_api_id');
	$maurisco_api_key = get_option( 'maurisco_api_key');

	get_transient( 'maurisco_lead_types_t' );
	$type_arr = maurisco_cf_get_leadtypes();


	$event_type_id = maurisco_filter_lead_type($type_arr, $event_type);

	if( defined(MARUISCO_CF_DEBUG) ){
		$url = 'https://192.168.1.157:8000/api/v1/lead';
	} else {
		$url = 'https://mauris.co/api/v1/lead';
	}

	$data = array(
		'apiId' => $maurisco_api_id,
		'apiKey' => $maurisco_api_key,
		'event_date' => $event_date,
		'clients' => array('email_0' => $email_0, 'name_0' => $name_0, 'name_1' => $name_1, 'name_2' => $name_2),
		'phone' => $phone,
		'event_location_1' => $event_location_1,
		'event_type' => $event_type,
		'type' => $event_type_id,
		'question_1' => $question_1,
		'question_2' => $question_2,
		'comment_1' => $comment_1,
		'ip' => $userIp
		);

	$result = wp_remote_post( $url, array( 'sslverify' => false, 'body' => $data ) );

	error_log(serialize($result));
	$response = array(
	   'what'=>'maurisco_cf_form',
	   'action'=>'post inquiry',
	   'id'=>'1',
	   'data'=>'<p>OK</p>'
	);
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();
	die();
}

function maurisco_filter_lead_type( $type_arr, $type_name ){
	foreach ($type_arr as $type){

		error_log('------------- type_arr from ajax endpoint -------------');
		error_log(($type->{'description'}));
		error_log(($type->{'name'}));
		error_log(($type->{'_id'}));

		if( strcasecmp( $type_name, $type->{'name'} ) == 0 )
			return $type->{'_id'};
	}
	return 0;
}


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
// if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-mauriscocf-admin.php' );
	add_action( 'plugins_loaded', array( 'Maurisco_Contact_Form_Plugin_Admin', 'get_instance' ) );

}
