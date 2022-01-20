<?php
/**
Plugin Name: Reading Position Indicator
Plugin URI: https://wordpress.org/plugins/reading-position-indicator/
Description: PLUGIN_TAGLINE
Author: Marcin Pietrzak
Text Domain: reading-position-indicator
Version: PLUGIN_VERSION
Author URI: http://iworks.pl/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2017-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

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
 * i18n
 */
function irpi_load_plugin_textdomain() {
	load_plugin_textdomain(
		'reading-position-indicator',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages/'
	);
}
add_action( 'plugins_loaded', 'irpi_load_plugin_textdomain' );

/**
 * load options
 */
/**
 * load options
 */

global $iworks_reading_position_indicator_options;
$iworks_reading_position_indicator_options = iworks_reading_position_indicator_get_options_object();

function iworks_reading_position_indicator_get_options_object() {
	global $iworks_reading_position_indicator_options;
	if ( is_object( $iworks_reading_position_indicator_options ) ) {
		return $iworks_reading_position_indicator_options;
	}
	$iworks_reading_position_indicator_options = new iworks_options();
	$iworks_reading_position_indicator_options->set_option_function_name( 'iworks_reading_position_indicator_options' );
	$iworks_reading_position_indicator_options->set_option_prefix( 'irpi_' );
	$iworks_reading_position_indicator_options->init();
	return $iworks_reading_position_indicator_options;
}

function iworks_reading_position_indicator_activate() {
	$iworks_reading_position_indicator_options = get_iworks_reading_position_indicator_options();
	$iworks_reading_position_indicator_options->activate();
}

function iworks_reading_position_indicator_deactivate() {
	$iworks_reading_position_indicator_options->set_option_prefix( iworks_reading_position_indicator );
	$iworks_reading_position_indicator_options->deactivate();
}
/**
 * start
 */
new iworks_position();

/**
 * Register to iWorks Rate!
 */
include_once dirname( __FILE__ ) . '/includes/iworks/rate/rate.php';
do_action(
	'iworks-register-plugin',
	plugin_basename( __FILE__ ),
	__( 'Reading Position Indicator ', 'reading-position-indicator' ),
	'reading-position-indicator'
);

