<?php

/**
 * Fired during plugin activation
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tesw
 * @subpackage Tesw/includes
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw_Activator
{
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function tesw_activate()
	{
		$tesw_wishlist_page_title = esc_html__('Wishlist', 'smart-wishlist-for-woocommerce');
		$tesw_wishlist_page_slug = sanitize_title($tesw_wishlist_page_title);

		// Check if the page doesn't already exist
		$tesw_wishlist_page = get_page_by_path($tesw_wishlist_page_slug);

		if (!$tesw_wishlist_page) {
			// Create a new page post
			$tesw_page_args = array(
				'post_title' => $tesw_wishlist_page_title,
				'post_content' =>'[tesw_smart_wishlist]',  //short-code added as post content to show in the whishlist page.
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_name' => $tesw_wishlist_page_slug,
			);

			$tesw_page_id = wp_insert_post($tesw_page_args);
			// Set the new page as the wishlist page
			$tesw_selected_page['tesw_page_show'] = $tesw_page_id;
			update_option('tesw_general_settings_fields', $tesw_selected_page);
			// Store the success message in a variable
			$tesw_success_message = esc_html__('Wishlist page created with ID:', 'smart-wishlist-for-woocommerce') . ' ' . esc_html($tesw_page_id);
		} else {
			// Update the existing wishlist page ID only if the admin has selected it
			$tesw_selected_page = get_option('tesw_general_settings_fields');
			$tesw_admin_selected_page = isset($tesw_selected_page['tesw_page_show']) ? $tesw_selected_page['tesw_page_show'] : '';

			if ($tesw_admin_selected_page == $tesw_wishlist_page->ID) {
				$tesw_selected_page['tesw_page_show'] = $tesw_wishlist_page->ID;
				update_option('tesw_general_settings_fields', $tesw_selected_page);
			}
			// Store the success message in a variable
			$tesw_success_message = esc_html__('Wishlist page already exists with ID:', 'smart-wishlist-for-woocommerce') . ' ' . esc_html($tesw_wishlist_page->ID);
		}

		// Display the success message outside of the activation function
		add_action('admin_notices', function () use ($tesw_success_message) {
			echo '<div class="tesw-notice notice notice-success is-dismissible"><p>' . esc_html($tesw_success_message) . '</p></div>';
		});
		
	}
	

}