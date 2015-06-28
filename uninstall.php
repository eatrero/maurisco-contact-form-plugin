<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Maurisco_Contact_Form_Plugin
 * @author    Ed Atrero <info@mauris.co>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins
 * @copyright 2013 Ed Atrero
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO: Define uninstall functionality here