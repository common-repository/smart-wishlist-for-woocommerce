<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tesw
 * @subpackage Tesw/includes
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function tesw_load_plugin_textdomain() {

		load_plugin_textdomain(
			'smart-wishlist-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
