<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tesw
 * @subpackage Tesw/public
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $tesw_plugin_name    The ID of this plugin.
	 */
	private $tesw_plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $tesw_version    The current version of this plugin.
	 */
	private $tesw_version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $tesw_plugin_name       The name of the plugin.
	 * @param      string    $tesw_version    The version of this plugin.
	 */
	public function __construct($tesw_plugin_name, $tesw_version)
	{

		$this->tesw_plugin_name = $tesw_plugin_name;
		$this->tesw_version = $tesw_version;

	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function tesw_enqueue_styles()
	{
		wp_enqueue_style($this->tesw_plugin_name, plugin_dir_url(__FILE__) . 'css/tesw-public.css', array(), $this->tesw_version, 'all');

	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	
	public function tesw_enqueue_scripts()
	{
		// Enqueue the tesw-public-wishlist.js script
		wp_enqueue_script('tesw-public-wishlist', plugin_dir_url(__FILE__) . 'js/tesw-public-wishlist.js', array('jquery'), '1.0.0', false);
	
		// Enqueue the tesw-public.js script
		wp_enqueue_script('tesw-public', plugin_dir_url(__FILE__) . 'js/tesw-public-remove-wishlist.js', array('jquery'), '1.0.0', false);
	
		// Generate and localize the nonce token and other localized messages
		$tesw_localized_data = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('tesw_wishlist_nonce'),
			'remove_success' => esc_html__('Product removed successfully.', 'smart-wishlist-for-woocommerce'),
			'remove_error' => esc_html__('An error occurred while removing the product.', 'smart-wishlist-for-woocommerce'),
			'remove_error_wishlist' => esc_html__('An error occurred while product adding.', 'smart-wishlist-for-woocommerce'),
			'no_products_selected' => esc_html__('Please select products to remove.', 'smart-wishlist-for-woocommerce'),
			'no_products_select_add_into_cart' => esc_html__('Please select products to add in cart.', 'smart-wishlist-for-woocommerce'),
			'empty_wishlist_message' => esc_html__('Your wishlist is empty.', 'smart-wishlist-for-woocommerce'),
			'product_added_message' => esc_html__('Product added to wishlist.', 'smart-wishlist-for-woocommerce'), // Localized alert message
			'copy_error_message' => esc_html__('Unable to copy to clipboard', 'smart-wishlist-for-woocommerce'), // Add this line for the copy error message

		);
	
		wp_localize_script('tesw-public', 'tesw_ajax_object', $tesw_localized_data);
	}
	/**
	 * Callback function for adding the "Add to Wishlist" button on the Shop Page and product page.
	 * 
	 * This function determines whether the button should be displayed based on the current page and user settings.
	 * If the button should be displayed, it generates the HTML markup for the button, message, and view wishlist link.
	 * It also defines the onclick event for the button to display a message and handle login requirements.
	 * 
	 * Note: This function serves as a demonstration and should be hooked to the appropriate action or filter.
	 * @since    1.0.0
	 */
	function tesw_wishlistify_add_wishlist_button()
	{
		global $product;

		$tesw_wishlist_product_page = isset(get_option('tesw_general_settings_fields')['tesw_wishlist_product_page']) ? sanitize_text_field(get_option('tesw_general_settings_fields')['tesw_wishlist_product_page']) : false;
		$tesw_settings = get_option('tesw_general_settings_fields');
		$tesw_enable_wishlist_button = isset($tesw_settings['tesw_enable_wishlist_button']) ? sanitize_text_field($tesw_settings['tesw_enable_wishlist_button']) : '0';

		// Retrieve the wishlist array from user meta
		$tesw_user_id = get_current_user_id();
		$tesw_wishlists = get_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', true) ?: array();
		
		$tesw_product_id = absint($product->get_id());

		// Check if the product exists in any of the wishlists
		$tesw_product_in_wishlist = false;
		foreach ($tesw_wishlists as $tesw_wishlist_name => $tesw_wishlist_data) {
			if (isset($tesw_wishlist_data['product_ids']) && is_array($tesw_wishlist_data['product_ids']) && in_array($tesw_product_id, $tesw_wishlist_data['product_ids'])) {
				$tesw_product_in_wishlist = true;
				break;
			}
		}

		if ($tesw_product_in_wishlist && (is_shop() || ($tesw_wishlist_product_page && is_product())) && $tesw_enable_wishlist_button === '1') {
			$tesw_options = get_option('tesw_add_to_wishlist_options_fields');
			$tesw_view_button_type_options = isset($tesw_options['tesw_view_button_type_options']) ? sanitize_text_field($tesw_options['tesw_view_button_type_options']) : 'tesw_show_view_wishlist';
			$tesw_view_wishlist_text = isset($tesw_options['tesw_view_wishlist_text']) ? sanitize_text_field($tesw_options['tesw_view_wishlist_text']) : '';
			$tesw_wishlist_page_url = get_permalink(get_option('tesw_general_settings_fields')['tesw_page_show']);

			if ($tesw_view_button_type_options === 'tesw_show_view_wishlist_icon') {
				// Show "View Wishlist" icon
				?>
				<div class="tesw-success-message"></div>
				<button class="tesw-view-wishlist-button tesw-button"><a href="<?php echo esc_url($tesw_wishlist_page_url); ?>"><i
							class="fa fa-eye" aria-hidden="true"></i></a></button>
				<?php
			} elseif (!empty($tesw_view_wishlist_text)) {
				// Show customized "View Wishlist" text
				?>
				<button class="tesw-view-wishlist-button tesw-button"><a href="<?php echo esc_url($tesw_wishlist_page_url); ?>"><?php echo esc_html($tesw_view_wishlist_text); ?></a></button>
				<?php
			} else {
				// Show default "View Wishlist" button
				?>
				<button class="tesw-view-wishlist-button tesw-button"><a href="<?php echo esc_url($tesw_wishlist_page_url); ?>"><?php esc_html_e('View Wishlist', 'smart-wishlist-for-woocommerce'); ?></a></button>
				<?php
			}
		} else {

			if ($tesw_enable_wishlist_button === '1' && (is_shop() || ($tesw_wishlist_product_page && is_product()))) {

				// The product is not in the wishlist, display "Add to Wishlist" button
				$tesw_options = get_option('tesw_general_settings_fields');
				$tesw_wishlist_name = isset($tesw_options['tesw_name_string']) ? sanitize_text_field($tesw_options['tesw_name_string']) : '';

				if (empty($tesw_wishlist_name)) {
					$tesw_wishlist_name = esc_html__('Add to Wishlist', 'smart-wishlist-for-woocommerce');
				}

				$tesw_wishlist_button = isset($tesw_options['tesw_wishlist_button']) ? sanitize_text_field($tesw_options['tesw_wishlist_button']) : '';
				$tesw_add_to_wishlist_icon = isset($tesw_options['tesw_add_to_wishlist_icon']) ? sanitize_text_field($tesw_options['tesw_add_to_wishlist_icon']) : 'fa fa-heart';
				$tesw_add_to_wishlist_both = isset($tesw_options['tesw_add_to_wishlist_both']) ? sanitize_text_field($tesw_options['tesw_add_to_wishlist_both']) : 'both';

				if ($tesw_wishlist_button === 'tesw_show_wishlist_icon' || $tesw_wishlist_button === 'tesw_show_wishlist_both') {
					// Display the icon
					?>
					<button class="tesw-wishlistify-button tesw-button tesw-add-to-wishlist"
						data-product-id="<?php echo esc_attr($tesw_product_id); ?>" data-user-id="<?php echo esc_attr($tesw_user_id); ?>">
						<?php
						$tesw_options_wishlist = get_option('tesw_add_to_wishlist_options_fields');
						// Customize the icon style
						$tesw_add_to_wishlist_icon_style = isset($tesw_options_wishlist['tesw_add_to_wishlist_icon_style']) ? sanitize_text_field($tesw_options_wishlist['tesw_add_to_wishlist_icon_style']) : '';

						if (!empty($tesw_add_to_wishlist_icon_style)) {
							echo '<i class="' . esc_attr($tesw_add_to_wishlist_icon_style) . '" aria-hidden="true"></i>';
						} else {
							echo '<i class="' . esc_attr($tesw_add_to_wishlist_icon) . ' " aria-hidden="true"></i>';
						}
						// Display the text if both icon and text are selected
						if ($tesw_add_to_wishlist_both === 'both' && $tesw_wishlist_button === 'tesw_show_wishlist_both') {
							echo esc_html($tesw_wishlist_name);
						}
						?>
					</button>
					<?php

				} else {
					// Display the default button
					?>
					<button class="tesw-wishlistify-button tesw-button tesw-add-to-wishlist"
						data-product-id="<?php echo esc_attr($tesw_product_id); ?>"
						data-user-id="<?php echo esc_attr($tesw_user_id); ?>"><?php echo esc_html($tesw_wishlist_name); ?></button>
					<?php
				}
				// Display the unlogged user message
				?>
				<div class="tesw-unlogged-message">
					<?php esc_html_e('Please add this product to your wishlist.', 'smart-wishlist-for-woocommerce'); ?>
					<a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>"><?php esc_html_e('My Account', 'smart-wishlist-for-woocommerce'); ?></a>
				</div>
				<?php
			}
		}
	}

	/**
	 * Callback function for adding a product to the wishlist via AJAX.
	 * 
	 * This function handles the AJAX request sent when a user adds a product to their wishlist.
	 * It retrieves the product data from the POST request and updates the wishlist cookie.
	 * The product ID is added to the list of wishlist product IDs stored in the cookie.
	 * If the request is successful, it sends a JSON response with a success message.
	 * If the request is invalid, it sends a JSON response with an error message.
	 * @since    1.0.0
	 */
	function tesw_add_to_wishlist()
	{
		// Verify the nonce
		if ( ! isset( $_POST['teswnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['teswnonce'] ) ) , 'tesw_wishlist_nonce' ) )
		{
			// Nonce verification failed, handle the error
			$tesw_secure = esc_html__('Invalid nonce.', 'smart-wishlist-for-woocommerce');
			wp_send_json_error($tesw_secure);
		}

		if (isset($_POST['product_id'])) {
			$tesw_product_id = isset($_POST['product_id']) ? sanitize_key($_POST['product_id']) : '';

			// Get the user ID
			$tesw_user_id = get_current_user_id();

			// Validate the product ID
			if ($tesw_product_id > 0) {
				// Get the existing wishlist array from user meta
				$tesw_wishlists = get_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', true);
				if (!$tesw_wishlists) {
					$tesw_wishlists = array(); // Initialize an empty array if wishlists don't exist
				}

				$tesw_multi_wishlist_options = get_option('tesw_pro_version_settings_fields');
				$tesw_wishlist_collection_enabled = isset($tesw_multi_wishlist_options['tesw_wishlist_collection']) && $tesw_multi_wishlist_options['tesw_wishlist_collection'] === '1';

				if ($tesw_wishlist_collection_enabled) {
					// Wishlist collection is enabled, check if the wishlist name is provided
					if (isset($_POST['wishlist_name']) && !empty($_POST['wishlist_name'])) {
						$tesw_wishlist_name = sanitize_text_field($_POST['wishlist_name']);
					} else {
						// Default wishlist name if not provided
						$tesw_wishlist_name = 'MyWishlist';
					}

					// Check if the current wishlist exists, or create a new one
					if (!isset($tesw_wishlists[$tesw_wishlist_name])) {
						$tesw_wishlists[$tesw_wishlist_name] = array(
							'product_ids' => array(),
						);
					}

					// Check if the product ID already exists in the current wishlist
					if (!in_array($tesw_product_id, $tesw_wishlists[$tesw_wishlist_name]['product_ids'])) {
						// Add the product ID to the current wishlist
						$tesw_wishlists[$tesw_wishlist_name]['product_ids'][] = $tesw_product_id;

						// Update the wishlist in user meta
						update_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', $tesw_wishlists);

						// Send the updated wishlists array as the response data
						wp_send_json_success(
							array(
								'user_id' => absint($tesw_user_id),
								'wishlists' => $tesw_wishlists // Modified response key to represent the wishlists array
							)
						);
					} else {
						// Send a JSON error response indicating that the product is already in the wishlist
						wp_send_json_error(
							array(
								'message' => esc_html__('Product is already added to the wishlist.', 'smart-wishlist-for-woocommerce'),
								'user_id' => absint($tesw_user_id)
							)
						);
					}
				} else {
					// Wishlist collection is disabled, create a single wishlist
					if (!isset($tesw_wishlists['MyWishlist'])) {
						$tesw_wishlists['MyWishlist'] = array(
							'product_ids' => array(),
						);
					}

					// Check if the product ID already exists in the wishlist
					if (!in_array($tesw_product_id, $tesw_wishlists['MyWishlist']['product_ids'])) {
						// Add the product ID to the wishlist
						$tesw_wishlists['MyWishlist']['product_ids'][] = $tesw_product_id;

						// Update the wishlist in user meta
						update_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', $tesw_wishlists);

						// Send the updated wishlist array as the response data
						wp_send_json_success(
							array(
								'user_id' => absint($tesw_user_id),
								'wishlists' => $tesw_wishlists // Modified response key to represent the wishlists array
							)
						);
					} else {
						// Send a JSON error response indicating that the product is already in the wishlist
						wp_send_json_error(
							array(
								'message' => esc_html__('Product is already added to the wishlist.', 'smart-wishlist-for-woocommerce'),
								'user_id' => absint($tesw_user_id)
							)
						);
					}
				}
			} else {
				// Send a JSON error response for an invalid product ID
				wp_send_json_error(esc_html__('Invalid product ID.', 'smart-wishlist-for-woocommerce'));
			}
		} else {
			// Send a JSON error response for an invalid request
			wp_send_json_error(esc_html__('Invalid request.', 'smart-wishlist-for-woocommerce'));
		}
	}

	/**
	 * Callback function for removing a product from the wishlist via AJAX.
	 *
	 * This function handles the AJAX request sent when a user removes a product from their wishlist.
	 * It retrieves the product ID from the POST request and checks if it exists in the wishlist.
	 * It then generates the updated wishlist HTML using the updated list of product IDs.
	 * If the removal is successful, it sends a JSON response with the updated wishlist HTML.
	 * If the product ID is not found in the wishlist, it sends a JSON response with an error message.
	 * If the request is invalid, it sends a JSON response with an error message.
	 * @since    1.0.0
	 */
	function tesw_remove_product_from_wishlist()
	{
		// Verify the nonce
		if ( ! isset( $_POST['teswremovenonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['teswremovenonce'] ) ) , 'tesw_wishlist_nonce' ) )
		{
			// Nonce verification failed, handle the error
			$tesw_remove_nonce_msg = esc_html__('Invalid nonce.', 'smart-wishlist-for-woocommerce');
			wp_send_json_error($tesw_remove_nonce_msg);
		}

		if (isset($_POST['productID'])) {
			$tesw_productID = sanitize_key($_POST['productID']);

			// Validate the product ID
			if ($tesw_productID > 0) {
				// Get the user ID
				$tesw_user_id = get_current_user_id();

				// Get the existing wishlist array from user meta
				$tesw_wishlist = get_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', true);

				// Check if the wishlist exists and is not empty
				if ($tesw_wishlist && !empty($tesw_wishlist['MyWishlist']['product_ids'])) {
					// Find the index of the product ID to be removed
					$tesw_index = array_search($tesw_productID, $tesw_wishlist['MyWishlist']['product_ids']);

					if ($tesw_index !== false) {
						// Remove the product ID from the array
						unset($tesw_wishlist['MyWishlist']['product_ids'][$tesw_index]);

						// Update the wishlist in user meta
						update_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', $tesw_wishlist);

						// Generate the updated wishlist HTML (if needed)
						$tesw_updatedWishlistHTML = $this->tesw_wishlist_product_shortcode();

						// Send the updated wishlist HTML as the response (if needed)
						wp_send_json_success($tesw_updatedWishlistHTML);

						// Send a success response without the updated HTML (if the HTML is being generated on the client-side)
						wp_send_json_success();
					} else {
						// Send an error response if the product ID is not found in the wishlist
						wp_send_json_error(esc_html__('Product not found in wishlist.', 'smart-wishlist-for-woocommerce'));
					}
				} else {
					// Send an error response if the wishlist is empty or doesn't exist
					wp_send_json_error(esc_html__('Empty wishlist.', 'smart-wishlist-for-woocommerce'));
				}
			} else {
				// Send an error response for an invalid product ID
				wp_send_json_error(esc_html__('Invalid product ID.', 'smart-wishlist-for-woocommerce'));
			}
		} else {
			// Send an error response for an invalid request
			wp_send_json_error(esc_html__('Invalid request.', 'smart-wishlist-for-woocommerce'));
		}
	}

	/**
	 * Callback function for changing the button CSS style.
	 *
	 * This function retrieves the style options from the database and outputs the custom CSS style for the button based on the selected options.
	 * It applies the button radius, button color, button text size, button text style, button font family, and text color CSS styles to the button.
	 * The CSS styles are outputted inside the <style> tags.
	 *
	 * @since    1.0.0
	 */
	function tesw_change_button_css_style()
	{
		// Retrieve the style options from the database
		$tesw_options = get_option('tesw_style_options');

		// Retrieve and sanitize the button style values
		$tesw_button_radius = isset($tesw_options['tesw_button_radius']) ? sanitize_text_field($tesw_options['tesw_button_radius']) : '';
		$tesw_button_text_style = isset($tesw_options['tesw_button_text_style']) ? sanitize_text_field($tesw_options['tesw_button_text_style']) : '';
		$tesw_button_color = isset($tesw_options['tesw_button_color']) ? sanitize_hex_color($tesw_options['tesw_button_color']) : '';
		$tesw_button_color_enable = isset($tesw_options['tesw_button_color_enable']) ? $tesw_options['tesw_button_color_enable'] : '';

		// Retrieve and sanitize the text color style values
		$tesw_text_color_css = isset($tesw_options['tesw_text_color_css']) ? sanitize_hex_color($tesw_options['tesw_text_color_css']) : '';
		$tesw_text_color_enable = isset($tesw_options['tesw_text_color_enable']) ? $tesw_options['tesw_text_color_enable'] : '';

		// Retrieve and sanitize the button text size value
		$tesw_button_text_size = isset($tesw_options['tesw_button_text_size']) ? sanitize_text_field($tesw_options['tesw_button_text_size']) : '';

		// Check if CSS customization is enabled
		if (isset($tesw_options['tesw_enable_css']) && $tesw_options['tesw_enable_css'] == 1) {
			// If CSS customization is enabled, proceed to generate dynamic CSS style

			// Generate the dynamic CSS style
			$tesw_dynamic_css = "
	    .tesw-button {
	        border-radius: {$tesw_button_radius}px;
	        font-style: {$tesw_button_text_style};
	        " . ($tesw_button_color_enable == 1 ? "background-color: {$tesw_button_color};" : "") . "
	        " . ($tesw_text_color_enable == 1 ? "color: {$tesw_text_color_css};" : "") . "
	        " . ($tesw_button_text_size ? "font-size: {$tesw_button_text_size}px;" : "") . " }	";

			// Register the dynamic CSS as an external stylesheet
			//This function registers a new stylesheet named 'tesw-dynamic-style'
			wp_register_style('tesw-dynamic-style', false);
			//This function adds the registered 'tesw-dynamic-style' stylesheet to the list of stylesheets to be loaded on the page.
			wp_enqueue_style('tesw-dynamic-style');
			//This function adds the generated $tesw_dynamic_css as inline styles to the 'tesw-dynamic-style' stylesheet. 
			wp_add_inline_style('tesw-dynamic-style', $tesw_dynamic_css);
			// The dynamic CSS will be added to the 'tesw-dynamic-style' stylesheet and loaded on the frontend.
		}

	}

	/**
	 * Callback function for removing multiple products from the wishlist via AJAX.
	 *
	 * This function handles the removal of multiple products from the user's wishlist
	 * when an AJAX request is made. It verifies the nonce for security, checks if the user
	 * is logged in and has the necessary capabilities to perform the action, validates the
	 * received data, and updates the user's wishlist by removing the selected products.
	 * It returns a JSON response indicating the success or failure of the operation.
	 *
	 * @since 1.0.0
	 */
	function tesw_remove_multiple_products_from_wishlist()
	{
		// Verify the nonce
		if ( ! isset( $_POST['teswmultiremovenonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['teswmultiremovenonce'] ) ) , 'tesw_wishlist_nonce' ) )
		{
			// Nonce verification failed, handle the error
			wp_send_json_error(esc_html__('Invalid nonce.', 'smart-wishlist-for-woocommerce'));
		}

		// Check if the user is logged in or has necessary capabilities to perform this action
		if (!is_user_logged_in() || !current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You are not allowed to perform this action.', 'smart-wishlist-for-woocommerce'));
		}

		// Check if the required data is received
		if (!isset($_POST['productIDs']) || !is_array($_POST['productIDs'])) {
			wp_send_json_error(esc_html__('Invalid data.', 'smart-wishlist-for-woocommerce'));
		}

		$tesw_productIDs = array_map('intval', $_POST['productIDs']);

		// Get the user ID
		$tesw_user_id = get_current_user_id();

		// Get the existing wishlist array from user meta
		$tesw_wishlist = get_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', true);

		if ($tesw_wishlist && is_array($tesw_wishlist) && !empty($tesw_wishlist)) {
			// Remove the selected product IDs from the wishlist array
			$tesw_updated_wishlist = array();
			foreach ($tesw_wishlist as $tesw_wishlist_key => $tesw_wishlist_data) {
				if (isset($tesw_wishlist_data['product_ids']) && is_array($tesw_wishlist_data['product_ids'])) {
					$tesw_updated_wishlist[$tesw_wishlist_key] = array(
						'product_ids' => array_diff($tesw_wishlist_data['product_ids'], $tesw_productIDs),
					);
				}
			}

			// Update the wishlist in user meta
			update_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', $tesw_updated_wishlist);

			// Send a success response back to the AJAX request
			wp_send_json_success(esc_html__('Products removed successfully.', 'smart-wishlist-for-woocommerce'));
		} else {
			// Send an error response if the wishlist is empty or doesn't exist
			wp_send_json_error(esc_html__('Empty wishlist.', 'smart-wishlist-for-woocommerce'));
		}
	}

	/**
	 * Callback function for adding multiple products to the cart via AJAX.
	 *
	 * This function handles the addition of multiple products to the cart when an AJAX request is made.
	 * It verifies the nonce for security, checks if the user is logged in and has the necessary capabilities
	 * to perform the action, validates the received data, and adds each selected product to the cart.
	 * After successfully adding products to the cart, it returns a JSON response with the updated cart count.
	 *
	 * @since 1.0.0
	 */
	function tesw_add_multiple_to_cart()
	{
		// Verify the nonce
		if ( ! isset( $_POST['teswmultinoncecart'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['teswmultinoncecart'] ) ) , 'tesw_wishlist_nonce' ) )
		{
			// Nonce verification failed, handle the error
			wp_send_json_error(esc_html__('Invalid nonce.', 'smart-wishlist-for-woocommerce'));
		}

		// Check if the user is logged in or has necessary capabilities to perform this action
		if (!is_user_logged_in() || !current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You are not allowed to perform this action.', 'smart-wishlist-for-woocommerce'));
		}

		// Get the user ID
		$tesw_user_id = get_current_user_id();

		// Get the selected product IDs from the AJAX request
		$tesw_productIDs = map_deep( $_POST['productIDs'], 'sanitize_text_field' );
		
		// Validate the product IDs
		$tesw_validatedProductIDs = array_map('intval', $tesw_productIDs);
		$tesw_validatedProductIDs = array_filter($tesw_validatedProductIDs, function ($id) {
			return $id > 0;
		});

		if (empty($tesw_validatedProductIDs)) {
			wp_send_json_error(esc_html__('No valid product IDs provided.', 'smart-wishlist-for-woocommerce'));
		}

		// Loop through the validated product IDs and add each product to the cart
		foreach ($tesw_validatedProductIDs as $tesw_productID) {
			WC()->cart->add_to_cart($tesw_productID);
		}

		// Return the updated cart data
		$tesw_cart_count = WC()->cart->get_cart_contents_count();

		// Prepare the response
		$tesw_response = array(
			'cart_count' => $tesw_cart_count,
			'message' => esc_html__('Products added to cart successfully.', 'smart-wishlist-for-woocommerce'),
		);

		// Send the success response back to the AJAX request
		wp_send_json_success($tesw_response);
	}

	/**
	 * Shortcode callback function for displaying the wishlist product table.
	 * 
	 * This function retrieves the product IDs stored in the cookie and generates the HTML markup for the wishlist table.
	 * It loops through each product ID, retrieves the product details, and adds a row to the table with the product name, price, and "Add to Cart" button.
	 * If there are no product IDs in the cookie, it displays a message indicating an empty wishlist.
	 * 
	 * Note: This function should be used as a shortcode callback and placed in the appropriate shortcode handler.
	 * @since    1.0.0
	 */
	function tesw_wishlist_product_shortcode()
	{
		// Get the user ID
		$tesw_user_id = get_current_user_id();
		$tesw_wishlists = get_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', true);

		$tesw_wishlist_table_columns = get_option('tesw_wishlist_page_options_fields');
		$tesw_social_setting = get_option('tesw_social_networks_settings_fields');

		$tesw_wishlist_table_columns_settings = isset($tesw_wishlist_table_columns['tesw_wishlist_table_columns']) ? $tesw_wishlist_table_columns['tesw_wishlist_table_columns'] : array();
		$tesw_social_networks = isset($tesw_social_setting['tesw_social_networks']) ? $tesw_social_setting['tesw_social_networks'] : array();
		$tesw_social_networks_show_icon = isset($tesw_social_setting['tesw_social_networks_show_icon']) ? $tesw_social_setting['tesw_social_networks_show_icon'] : 0;
		$tesw_enable_social_sharing = isset($tesw_social_setting['tesw_enable_social_sharing']) ? $tesw_social_setting['tesw_enable_social_sharing'] : 0;
		
		// Check if the wishlist exists and is not empty
		if ($tesw_wishlists['MyWishlist']['product_ids'] && !empty($tesw_wishlists['MyWishlist']['product_ids'])) {
			$tesw_html = '';

			// Generate the HTML markup for the wishlist table using the product IDs
			$tesw_html .= '<table id="tesw-wishlist-table">
				<tr>
					<th>' . esc_html__('Select', 'smart-wishlist-for-woocommerce') . '</th>
					<th>' . esc_html__('Product Image', 'smart-wishlist-for-woocommerce') . '</th>
					<th>' . esc_html__('Product Name', 'smart-wishlist-for-woocommerce') . '</th>
					<th>' . esc_html__('Price', 'smart-wishlist-for-woocommerce') . '</th>';

			// Check if the 'product_variations' column is enabled in admin
			if (is_array($tesw_wishlist_table_columns_settings) && in_array('product_variations', $tesw_wishlist_table_columns_settings)) {
				$tesw_html .= '<th>' . esc_html__('Product Variations', 'smart-wishlist-for-woocommerce') . '</th>';
			}

			// Check if the 'product_stock' column is enabled in admin
			if (is_array($tesw_wishlist_table_columns_settings) && in_array('product_stock', $tesw_wishlist_table_columns_settings)) {
				$tesw_html .= '<th>' . esc_html__('Product Stock', 'smart-wishlist-for-woocommerce') . '</th>';
			}

			// Check if the 'date_added' column is enabled in admin
			if (is_array($tesw_wishlist_table_columns_settings) && in_array('date_added', $tesw_wishlist_table_columns_settings)) {
				$tesw_html .= '<th>' . esc_html__('Date Added', 'smart-wishlist-for-woocommerce') . '</th>';
			}

			$tesw_html .= '<th>' . esc_html__('Product Add To Cart', 'smart-wishlist-for-woocommerce') . '</th>
					<th>' . esc_html__('Action', 'smart-wishlist-for-woocommerce') . '</th>
				</tr>';

			// Get the product IDs from the user meta (default wishlist or "My Wishlist")
			if ($tesw_wishlists && isset($tesw_wishlists['MyWishlist']) && is_array($tesw_wishlists['MyWishlist']['product_ids'])) {
				foreach ($tesw_wishlists['MyWishlist']['product_ids'] as $tesw_product_id) {
					// Get the product details based on the product ID
					$tesw_product = wc_get_product($tesw_product_id);

					// Check if the product exists
					if (!$tesw_product) {
						continue; // Skip this product if it doesn't exist
					}
					$tesw_product_image = $tesw_product->get_image();

					// Get the product variations
					$tesw_product_variations = '';
					if (is_array($tesw_wishlist_table_columns_settings) && in_array('product_variations', $tesw_wishlist_table_columns_settings)) {
						$tesw_product_variations = $this->tesw_get_product_variations($tesw_product);
					}

					// Get the product stock
					$tesw_product_stock = '';
					if (is_array($tesw_wishlist_table_columns_settings) && in_array('product_stock', $tesw_wishlist_table_columns_settings)) {
						$tesw_product_stock = $this->tesw_get_product_stock($tesw_product);
					}

					// Get the date added
					$tesw_date_added = '';
					if (is_array($tesw_wishlist_table_columns_settings) && in_array('date_added', $tesw_wishlist_table_columns_settings)) {
						$tesw_date_added = $this->tesw_get_product_date_added($tesw_product_id);
					}

					// Build the HTML table row for each product
					$tesw_html .= '<tr>
							<td><input type="checkbox" class="tesw-product-checkbox" data-product-id="' . $tesw_product_id . '"></td>
							<td class="tesw-img"><a href="' . esc_url(get_permalink($tesw_product->get_id())) . '">' . $tesw_product_image . '</a></td>
							<td>' . esc_html($tesw_product->get_name()) . '</td>
							<td>' . $tesw_product->get_price_html() . '<span class="tesw-get-price-html" >' . $tesw_product->get_price() . '</span></td>';

					// Add the product variations column if enabled
					if (is_array($tesw_wishlist_table_columns_settings) && in_array('product_variations', $tesw_wishlist_table_columns_settings)) {
						$tesw_html .= '<td>' . esc_html(sanitize_text_field($tesw_product_variations)) . '</td>';
					}

					// Add the product stock column if enabled
					if (is_array($tesw_wishlist_table_columns_settings) && in_array('product_stock', $tesw_wishlist_table_columns_settings)) {
						$tesw_html .= '<td>' . esc_html(sanitize_text_field($tesw_product_stock)) . '</td>';
					}

					// Add the date added column if enabled
					if (is_array($tesw_wishlist_table_columns_settings) && in_array('date_added', $tesw_wishlist_table_columns_settings)) {
						$tesw_html .= '<td>' . esc_html(sanitize_text_field($tesw_date_added)) . '</td>';
					}

					// Modify the "Add to Cart" button to include data-product-ids attribute
					$tesw_html .= '<td><a href="' . esc_url($tesw_product->add_to_cart_url()) . '" class="tesw-button-cart button tesw-button">' . esc_html__('Add to Cart', 'smart-wishlist-for-woocommerce') . '</a></td>';

					$tesw_html .= '<td><button class="tesw-remove-product tesw-button" data-product-id="' . $tesw_product_id . '">' . esc_html__('Remove', 'smart-wishlist-for-woocommerce') . '</button></td>
						</tr>';
				}
			}
			$tesw_html .= '</table>';
			
			$tesw_html .= '<div class="tesw-action-buttons">
			<select class="tesw-action-select tesw-button">
				<option value="">' . esc_html__('Action', 'smart-wishlist-for-woocommerce') . '</option>
				<option value="tesw-multiple-add-to-cart">' . esc_html__('Add to Cart', 'smart-wishlist-for-woocommerce') . '</option>
				<option value="remove">' . esc_html__('Remove', 'smart-wishlist-for-woocommerce') . '</option>
			</select>
			<button id="tesw-apply-button" class="tesw-button">' . esc_html__('Apply Action', 'smart-wishlist-for-woocommerce') . '</button>
			</div>';
		}else {
				// If the wishlist is empty, do not display the table heading and apply action button
				$tesw_html = '<div class="tesw-wishlist-empty-message">' . esc_html__('Your Wishlist is Empty!', 'smart-wishlist-for-woocommerce') . '</div><br>';
				$tesw_html .= '<div class="tesw-return-to-shop-button">';
				$tesw_html .= '<a href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '" class="tesw-return-button button tesw-button">' . esc_html__('Return to Shop', 'smart-wishlist-for-woocommerce') . '</a>';
				$tesw_html .= '</div>';
			}
			
			// Check if the "Enable Social Sharing" option is enabled
			if ($tesw_enable_social_sharing) {
				// Add the heading for social networks
				$tesw_html .= '<h2>' . esc_html__('Wishlists Share on Social Networks', 'smart-wishlist-for-woocommerce') . '</h2>';

				// Add the share buttons for social networks
				$tesw_html .= '<div class="tesw-social-share-buttons " id="tesw-social-share-buttons">';
				if (in_array('whatsapp', $tesw_social_networks)) {
					// Get the wishlist page URL dynamically using get_permalink()
					$tesw_wishlist_link = get_permalink(); // Assuming the current page is the wishlist page

					// Create the WhatsApp share message with the wishlist link
					$tesw_whatsapp_message = esc_html__('Check out my wishlist: ','smart-wishlist-for-woocommerce') . $tesw_wishlist_link;

					// Add the WhatsApp share button
					$tesw_whatsapp_share_url = 'https://web.whatsapp.com/send?text=' . urlencode($tesw_whatsapp_message);
					$tesw_html .= '<a href="' . esc_url($tesw_whatsapp_share_url) . '" target="_blank" class="tesw-social-share-button tesw-whatsapp-share ">';
					if ($tesw_social_networks_show_icon) {
						$tesw_html .= '<i class="fab fa-whatsapp"></i>';
					}
					$tesw_html .= esc_html__('WhatsApp', 'smart-wishlist-for-woocommerce') . '</a>';
				}
				// Assuming $tesw_social_networks contains the list of enabled social networks
				if (in_array('gmail', $tesw_social_networks)) {
					// Get the wishlist page URL dynamically using get_permalink()
					$tesw_wishlist_link = get_permalink();

					// Generate the Gmail share URL with the wishlist link pre-filled in the body of the email
					$tesw_gmail_share_url = 'mailto:?body=' . urlencode($tesw_wishlist_link);

					// Add the Gmail share link with the icon
					$tesw_html .= '<a href="' . esc_url($tesw_gmail_share_url) . '" target="_blank" class="tesw-social-share-button tesw-gmail-share">';
					if ($tesw_social_networks_show_icon) {
						$tesw_html .= '<i class="fa fa-envelope"></i>'; // Using FontAwesome icon class
					}
					$tesw_html .= esc_html__('Gmail', 'smart-wishlist-for-woocommerce') . '</a>';
				}

				if (in_array('pinterest', $tesw_social_networks)) {
					$tesw_html .= '<a href="https://pinterest.com/pin/create/button/?url=' . get_permalink() . '" target="_blank" class="tesw-social-share-button tesw-pinterest-share">';
					if ($tesw_social_networks_show_icon) {
						$tesw_html .= '<i class="fab fa-pinterest"></i>';
					}
					$tesw_html .= esc_html__('Pinterest', 'smart-wishlist-for-woocommerce') . '</a>';
				}

				if (in_array('twitter', $tesw_social_networks)) {
					$tesw_html .= '<a href="https://twitter.com/intent/tweet?url=' . get_permalink() . '" target="_blank" class="tesw-social-share-button tesw-twitter-share">';
					if ($tesw_social_networks_show_icon) {
						$tesw_html .= '<i class="fab fa-twitter"></i>';
					}
					$tesw_html .= esc_html__('Twitter', 'smart-wishlist-for-woocommerce') . '</a>';
				}

				if (in_array('copy_link', $tesw_social_networks)) {
					$tesw_wishlist_link = esc_url(get_permalink());
					$tesw_html .= '<a href="#" class="tesw-social-share-button tesw-copy-link-share" data-wishlist-link="' . $tesw_wishlist_link . '">';
					if ($tesw_social_networks_show_icon) {
						$tesw_html .= '<i class="fa fa-copy"></i>';
					}
					$tesw_html .= esc_html__('Copy Link', 'smart-wishlist-for-woocommerce') . '</a>';
				}
				$tesw_html .= '<p id="tesw-copy-message">' . esc_html__('Wishlist link copied to clipboard!', 'smart-wishlist-for-woocommerce') . '</p>';

				$tesw_html .= '</div>';
			}
			return $tesw_html;

	}

	/**
	 * Retrieves the variations of a product.
	 * 
	 * This function is used to get the variations of a product.
	 * It takes a product object as input and returns an array of variations.
	 * 
	 * @since 1.0.0
	 */
	function tesw_get_product_variations($tesw_product)
	{
		// Retrieve the variations of a variable product
		if ($tesw_product->is_type('variable')) {
			$tesw_variations = $tesw_product->get_available_variations();
			if (!empty($tesw_variations)) {
				$tesw_variation_names = array();
				foreach ($tesw_variations as $tesw_variation) {
					$tesw_variation_names[] = implode(', ', $tesw_variation['attributes']);
				}
				return implode('<br>', $tesw_variation_names);
			}
		}
		return '-';
	}
	/**
	 * Retrieves the stock status of a product.
	 * 
	 * This function is used to get the stock status of a product.
	 * It takes a product object as input and returns the stock information.
	 * 
	 * @since 1.0.0
	 */
	function tesw_get_product_stock($tesw_product)
	{
		// Retrieve the product stock status
		$tesw_stock_status = $tesw_product->get_stock_status();
		if ($tesw_stock_status === 'instock') {

			return esc_html__('In Stock', 'smart-wishlist-for-woocommerce');

		} elseif ($tesw_stock_status === 'outofstock') {

			return esc_html__('Out of Stock', 'smart-wishlist-for-woocommerce');
		}
		return '-';
	}
	/**
	 * Retrieves the date added of a product.
	 * 
	 * This function is used to get the date when a product was added.
	 * It takes a product ID as input and returns the date added.
	 * 
	 * @since 1.0.0
	 */
	function tesw_get_product_date_added($tesw_product_id)
	{
		// Get the user ID
		$tesw_user_id = get_current_user_id();

		// Get the existing wishlist array from user meta
		$tesw_wishlist = get_user_meta($tesw_user_id, 'tesw_smart_wishlist_meta', true);

		// Check if the wishlist exists and is not empty
		if ($tesw_wishlist && !empty($tesw_wishlist)) {
			// Check if the product ID exists in the wishlist
			if (in_array($tesw_product_id, $tesw_wishlist)) {
				// Get the index of the product ID in the wishlist array
				$tesw_index = array_search($tesw_product_id, $tesw_wishlist);

				// Get the corresponding date added from the user meta
				$tesw_wishlist_dates = get_user_meta($tesw_user_id, 'tesw_wishlist_date_added', true);

				if ($tesw_wishlist_dates && isset($tesw_wishlist_dates[$tesw_index])) {
					return $tesw_wishlist_dates[$tesw_index];
				}
			}
		}

		// Default case: return the current date if the date added is not found
		$tesw_current_date = date('Y-m-d'); // Get the current date
		return $tesw_current_date;
	}
}
