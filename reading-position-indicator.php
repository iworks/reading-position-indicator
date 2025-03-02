<?php
/**
 * Reading Position Indicator
 *
 * @package           PLUGIN_NAME
 * @author            AUTHOR_NAME
 * @copyright         2017-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Reading Position Indicator
 * Plugin URI:        PLUGIN_URI
 * Description:       PLUGIN_DESCRIPTION
 * Version:           PLUGIN_VERSION
 * Requires at least: PLUGIN_REQUIRES_WORDPRESS
 * Requires PHP:      PLUGIN_REQUIRES_PHP
 * Author:            AUTHOR_NAME
 * Author URI:        AUTHOR_URI
 * Text Domain:       reading-position-indicator
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once dirname( __FILE__ ) . '/etc/options.php';

$includes = dirname( __FILE__ ) . '/includes';

if ( ! class_exists( 'iworks_options' ) ) {
	include_once $includes . '/iworks/options/options.php';
}
include_once $includes . '/iworks/class-iworks-position.php';

/**
 * load options
 */

global $iworks_reading_position_indicator_options;
$iworks_reading_position_indicator_options = null;

function iworks_reading_position_indicator_get_options_object() {
	global $iworks_reading_position_indicator_options;
	if ( is_object( $iworks_reading_position_indicator_options ) ) {
		return $iworks_reading_position_indicator_options;
	}
	$iworks_reading_position_indicator_options = new iworks_options();
	$iworks_reading_position_indicator_options->set_option_function_name( 'iworks_reading_position_indicator_options' );
	$iworks_reading_position_indicator_options->set_option_prefix( 'irpi_' );
	if ( method_exists( $iworks_reading_position_indicator_options, 'set_plugin' ) ) {
		$iworks_reading_position_indicator_options->set_plugin( basename( __FILE__ ) );
	}
	$iworks_reading_position_indicator_options->init();
	return $iworks_reading_position_indicator_options;
}

function iworks_reading_position_indicator_activate() {
	$iworks_reading_position_indicator_options = iworks_reading_position_indicator_get_options_object();
	$iworks_reading_position_indicator_options->activate();
}

function iworks_reading_position_indicator_deactivate() {
	$iworks_reading_position_indicator_options = iworks_reading_position_indicator_get_options_object();
	$iworks_reading_position_indicator_options->deactivate();
}
/**
 * start
 */
new iworks_position();

