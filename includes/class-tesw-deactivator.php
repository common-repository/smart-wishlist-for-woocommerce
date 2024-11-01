<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Tesw
 * @subpackage Tesw/includes
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function tesw_deactivate()
	{
		$tesw_wishlist_page_slug = sanitize_title('Wishlist');

		// Check if the page exists
		$tesw_wishlist_page = get_page_by_path($tesw_wishlist_page_slug);

		if ($tesw_wishlist_page) {
			// Delete the specific wishlist page
			wp_delete_post($tesw_wishlist_page->ID, true);

			// Display a success message
			add_action('admin_notices', function () {
				$tesw_success_message = esc_html__('Wishlist page has been removed.', 'smart-wishlist-for-woocommerce');
				echo '<div class="tesw-notice notice notice-success is-dismissible"><p>' . esc_html($tesw_success_message) . '</p></div>';
			});
		}
	}
}