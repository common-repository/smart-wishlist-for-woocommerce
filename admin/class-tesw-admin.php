<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tesw
 * @subpackage Tesw/admin
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw_Admin
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
	 * @param      string    $tesw_plugin_name       The name of this plugin.
	 * @param      string    $tesw_version    The version of this plugin.
	 */
	public function __construct($tesw_plugin_name, $tesw_version)
	{

		$this->tesw_plugin_name = $tesw_plugin_name;
		$this->tesw_version = $tesw_version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function tesw_enqueue_styles()
	{
		$tesw_current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		// Check if we are on the plugin's settings page
		if ($tesw_current_page === 'tesw-wishlist-plugin') {
			// Load the JavaScript only when on the plugin's settings page
			wp_enqueue_style($this->tesw_plugin_name, plugin_dir_url(__FILE__) . 'css/tesw-admin.css', array(), $this->tesw_version, 'all');
		}
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function tesw_enqueue_scripts()
	{
		$tesw_current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		// Check if we are on the plugin's settings page
		if ($tesw_current_page === 'tesw-wishlist-plugin') {
			// Load the JavaScript only when on the plugin's settings page
			wp_enqueue_script('tesw-admin', plugin_dir_url(__FILE__) . 'js/tesw-admin.js', array('jquery'), '1.0.0', false);
		}
	}

	/**
	 * Callback function for adding a menu page for the plugin.
	 *
	 * This function adds a menu page for the plugin in the WordPress admin menu.
	 * It defines the menu title, label, capability required to access the page,
	 * the unique slug for the page, the callback function to display the page,
	 * the icon for the menu item, and the position of the menu item in the admin menu.
	 * @since    1.0.0
	 */
	function tesw_wishlist_plugin_add_menu_page()
	{
		add_menu_page(
			esc_html__('Wishlist Plugin', 'smart-wishlist-for-woocommerce'),
			// The title of the menu page
			esc_html__('Smart Wishlist', 'smart-wishlist-for-woocommerce'),
			// The label displayed in the admin menu with orange color and bold font
			'manage_options',
			// The capability required to access the menu page
			'tesw-wishlist-plugin',
			// The unique slug for the menu page
			array($this, 'tesw_wishlist_plugin_settings_page'),
			// The callback function to display the settings page
			'dashicons-heart',
			// The icon for the menu page (make sure Dashicons library is properly loaded)
			20, // The position of the menu page in the admin menu
		);
	}
	/**
	 * Adds a custom menu class to the Wishlist Plugin menu item in the WordPress admin menu.
	 *
	 * @since 1.0.0
	 *
	 * This function iterates over the menu items and checks for the Wishlist Plugin menu item.
	 * Once found, it adds the 'tesw-plugin-menu' class to the menu item's existing class attribute.
	 * This allows for targeted styling or functionality for the Wishlist Plugin menu item.
	 */
	function tesw_add_custom_menu_class()
	{
		global $menu;

		foreach ($menu as $tesw_key => $tesw_item) {
			if ($tesw_item[2] === 'tesw-wishlist-plugin') {
				$menu[$tesw_key][4] .= ' tesw-plugin-menu';
				break;
			}
		}
	}

	/**
	 * Callback function for registering plugin settings.
	 *
	 * This function registers the settings and adds sections and fields for various options.
	 * It is responsible for defining the structure and functionality of the plugin's settings page.
	 * Each section represents a specific group of settings, and each field represents an individual setting option.
	 * The settings are registered using the WordPress settings API functions such as register_setting(),
	 * add_settings_section(), and add_settings_field().
	 * @since    1.0.0
	 */
	function tesw_register_settings()
	{
		// Register settings for General Settings
		register_setting('tesw_general_settings', 'tesw_general_settings_fields');
		add_settings_section('tesw_general_settings_section', '', array($this, 'tesw_general_settings_section_callback'), 'tesw_general_settings');
		
		add_settings_field('tesw_enable_wishlist_button', esc_html__('Enable Wishlist Button', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_enable_wishlist_button_callback'), 'tesw_general_settings', 'tesw_general_settings_section');
		add_settings_field('tesw_wishlist_product_page', esc_html__('Display "Add to Wishlist" Button on Product Page', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_wishlist_product_page_callback'), 'tesw_general_settings', 'tesw_general_settings_section');
		add_settings_field('tesw_wishlist_button_icon', esc_html__('Display Style for "Add to Wishlist" Button', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_wishlist_options_callback'), 'tesw_general_settings', 'tesw_general_settings_section');
		add_settings_field('tesw_default_wishlist_name', esc_html__('Customize "Add To Wishlist" Text', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_default_wishlist_name_callback'), 'tesw_general_settings', 'tesw_general_settings_section');
		add_settings_field('tesw_wishlist_page_filter', esc_html__('Select Wishlist Page', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_wishlist_page_filter_callback'), 'tesw_general_settings', 'tesw_general_settings_section');

		// Register settings for Add To Wishlist Options Section

		register_setting('tesw_add_to_wishlist_options', 'tesw_add_to_wishlist_options_fields');
		add_settings_section('tesw_add_to_wishlist_options_section', '', array($this, 'tesw_add_to_wishlist_options_section_callback'), 'tesw_add_to_wishlist_options');
		add_settings_field('tesw_view_button_type_options', esc_html__('View button type options', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_view_button_type_options_callback'), 'tesw_add_to_wishlist_options', 'tesw_add_to_wishlist_options_section');
		add_settings_field('tesw_view_wishlist_text', esc_html__('Customise "View wishlist" Text', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_view_wishlist_text_callback'), 'tesw_add_to_wishlist_options', 'tesw_add_to_wishlist_options_section');
		add_settings_field('tesw_add_to_wishlist_icon_style', esc_html__('Customise "Add to Wishlist" Icon', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_add_to_wishlist_icon_style_callback'), 'tesw_add_to_wishlist_options', 'tesw_add_to_wishlist_options_section');

		// Register settings for Wishlist Page Options Section

		register_setting('tesw_wishlist_page_options', 'tesw_wishlist_page_options_fields');
		add_settings_section('tesw_wishlist_page_options_section', '', array($this, 'tesw_wishlist_page_options_section_callback'), 'tesw_wishlist_page_options');
		add_settings_field('tesw_wishlist_table_columns', esc_html__('Wishlist Table Columns', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_wishlist_table_columns_callback'), 'tesw_wishlist_page_options', 'tesw_wishlist_page_options_section');

		// Register settings for Social Networks Section

		register_setting('tesw_social_networks_settings', 'tesw_social_networks_settings_fields');
		add_settings_section('tesw_social_networks_settings_section', '', array($this, 'tesw_social_networks_settings_section_callback'), 'tesw_social_networks_settings');
		add_settings_field('tesw_enable_social_sharing_field', esc_html__('Enable Social Sharing', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_enable_social_sharing_field_callback'), 'tesw_social_networks_settings', 'tesw_social_networks_settings_section');
		add_settings_field('tesw_social_networks_field', esc_html__('Select Social Networks', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_social_networks_field'), 'tesw_social_networks_settings', 'tesw_social_networks_settings_section');
		add_settings_field('tesw_social_networks_show_icon', esc_html__('Show Social Network Icons', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_social_networks_icon'), 'tesw_social_networks_settings', 'tesw_social_networks_settings_section');

		// Register settings for Style Section
		register_setting('tesw_style_settings', 'tesw_style_options');
		add_settings_section('tesw_style_settings_section', '', array($this, 'tesw_style_settings_section_callback'), 'tesw_style_settings');
		add_settings_field('tesw_enable_css_style_field', esc_html__('Enable Custom CSS', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_enable_css_style_field_callback'), 'tesw_style_settings', 'tesw_style_settings_section');
		add_settings_field('tesw_button_radius_options', esc_html__('Change Button Radius', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_button_radius_css_callback'), 'tesw_style_settings', 'tesw_style_settings_section');
		add_settings_field('tesw_button_color_options', esc_html__('Change Button Color', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_button_color_css_callback'), 'tesw_style_settings', 'tesw_style_settings_section');
		add_settings_field('tesw_text_color_options', esc_html__('Change Font Color', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_text_color_css_callback'), 'tesw_style_settings', 'tesw_style_settings_section');
		add_settings_field('tesw_button_text_size_options', esc_html__('Change Button Font Size', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_button_text_size_css_callback'), 'tesw_style_settings', 'tesw_style_settings_section');
		add_settings_field('tesw_button_text_style_options', esc_html__('Change Button Font Style', 'smart-wishlist-for-woocommerce'), array($this, 'tesw_button_text_style_css_callback'), 'tesw_style_settings', 'tesw_style_settings_section');
		
	}

	/**
	 * Callback function for the "General Settings" section.
	 *
	 * This function displays the description for the General Settings section.
	 * You can add your custom description or content for the General Settings section here.
	 * @since    1.0.0
	 */
	function tesw_general_settings_section_callback()
	{
		// Add your General Settings description or content here.
		return;
	}

	/**
	 * Callback function for enabling the wishlist button option.
	 *
	 * This function is responsible for rendering a toggle switch input on a settings page. Users can use this toggle switch to enable or disable a specific feature, such as the wishlist button.
	 * It retrieves the current value of the "tesw_enable_wishlist_button" option from the plugin's settings and sets the initial state of the toggle switch based on that value.
	 * @since    1.0.0
	 */
	function tesw_enable_wishlist_button_callback(){
		$tesw_settings = get_option('tesw_general_settings_fields');
		$tesw_enable_wishlist_button = isset($tesw_settings['tesw_enable_wishlist_button']) ? sanitize_text_field($tesw_settings['tesw_enable_wishlist_button']) : '0';
		?>
		<div class="tesw-toggle-switch">
			<input type="checkbox" id="tesw_enable_wishlist_button"
				name="tesw_general_settings_fields[tesw_enable_wishlist_button]" value="1" <?php checked('1', $tesw_enable_wishlist_button); ?>>
			<label for="tesw_enable_wishlist_button" class="tesw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Wishlist Product Page" field.
	 *
	 * This function displays the HTML code for the checkbox option to enable/disable the wishlist product page.
	 * It retrieves the current value of the "wishlist_product_page" option from the plugin's settings.
	 * The checkbox is checked or unchecked based on the saved option value.
	 * @since    1.0.0
	 */
	function tesw_wishlist_product_page_callback()
	{
		$tesw_general_settings = get_option('tesw_general_settings_fields');
		$tesw_wishlist_product_page = isset($tesw_general_settings['tesw_wishlist_product_page']) ? sanitize_text_field($tesw_general_settings['tesw_wishlist_product_page']) : false;
		?>
		<label class="tesw-toggle-switch">
			<input type="checkbox" name="tesw_general_settings_fields[tesw_wishlist_product_page]" value="1" <?php checked(1, $tesw_wishlist_product_page); ?>>
			<span class="tesw-toggle-slider"></span>
		</label>
		<?php
	}

	/**
	 * Callback function for the "Show Wishlist Button Types" field.
	 *
	 * This function displays the input field for the select wishlist name option.
	 * It retrieves the default wishlist name value from the options and displays it in the input field.
	 *
	 * @since 1.0.0
	 */
	function tesw_wishlist_options_callback()
	{

		$tesw_options = get_option('tesw_general_settings_fields');
		$tesw_wishlist_button = isset($tesw_options['tesw_wishlist_button']) ? sanitize_text_field($tesw_options['tesw_wishlist_button']) : '';

		?>
		<div class="tesw-wishlists-buttons-options">
			<label>
				<input type="radio" name="tesw_general_settings_fields[tesw_wishlist_button]" value="tesw_show_wishlist_button"
					<?php checked(empty($tesw_wishlist_button) || $tesw_wishlist_button === 'tesw_show_wishlist_button', true); ?>>
				<?php esc_html_e('Show "Add To Wishlist" Button', 'smart-wishlist-for-woocommerce'); ?>
			</label><br/>
			<label>
				<input type="radio" name="tesw_general_settings_fields[tesw_wishlist_button]" value="tesw_show_wishlist_icon"
					<?php checked($tesw_wishlist_button, 'tesw_show_wishlist_icon'); ?>>
				<?php esc_html_e('Show "Add To Wishlist" Icon', 'smart-wishlist-for-woocommerce'); ?>
			</label><br/>
			<label>
				<input type="radio" name="tesw_general_settings_fields[tesw_wishlist_button]" value="tesw_show_wishlist_both"
					<?php checked($tesw_wishlist_button, 'tesw_show_wishlist_both'); ?>>
				<?php esc_html_e('Show "Add To Wishlist" Icon and Text Both', 'smart-wishlist-for-woocommerce'); ?>
			</label>
		</div>

		<?php
	}

	/**
	 * Callback function for the "Default Wishlist Name" field.
	 *
	 * This function displays the input field for the default wishlist name option.
	 * It retrieves the default wishlist name value from the options and displays it in the input field.
	 * @since    1.0.0
	 */
	function tesw_default_wishlist_name_callback()
	{
		$tesw_default_name = get_option('tesw_general_settings_fields');
		$tesw_wishlist_name = isset($tesw_default_name['tesw_name_string']) ? $tesw_default_name['tesw_name_string'] : '';

		// Sanitize the input value
		$tesw_wishlist_name = sanitize_text_field($tesw_wishlist_name);
		?>
		<input class="tesw-input-field" id="tesw_default_wishlist_name" name="tesw_general_settings_fields[tesw_name_string]"
			type="text" value="<?php echo esc_attr($tesw_wishlist_name); ?>" />
		<?php
	}

	/**
	 * Callback function for the "Select Wishlist Page" field.
	 *
	 * This function displays the select field for choosing a page to show the wishlist on.
	 * It retrieves the selected page from the options and displays a list of pages in the select field.
	 * It also provides a link to create a new page and add the shortcode [tesw_smart_wishlist].
	 * @since    1.0.0
	 */
	function tesw_wishlist_page_filter_callback()
	{
		$tesw_selected_page = get_option('tesw_general_settings_fields');
		$tesw_wishlist_page = isset($tesw_selected_page['tesw_page_show']) ? sanitize_text_field($tesw_selected_page['tesw_page_show']) : '';
		$tesw_previous_wishlist_page = isset($tesw_selected_page['tesw_previous_page_show']) ? sanitize_text_field($tesw_selected_page['tesw_previous_page_show']) : '';

		// Sanitize the input values
		$tesw_wishlist_page = sanitize_text_field($tesw_wishlist_page);

		// Store the previous page ID
		$tesw_selected_page['tesw_previous_page_show'] = $tesw_wishlist_page;

		$tesw_pages = get_pages();

		if (!empty($tesw_pages)) {
			?>
			<select id="tesw_wishlist_page_filter" name="tesw_general_settings_fields[tesw_page_show]" class="tesw-input-field">
				<?php
				// Check if wishlist page exists
				$tesw_wishlist_page_exists = false;
				foreach ($tesw_pages as $tesw_page) {
					$tesw_selected = ($tesw_wishlist_page == $tesw_page->ID) ? 'selected="selected"' : '';

					if ($tesw_page->post_name === 'wishlist') {
						$tesw_wishlist_page_exists = true;
						?>
						<option value="<?php echo esc_attr($tesw_page->ID); ?>" <?php echo esc_attr($tesw_selected); ?>><?php echo esc_html($tesw_page->post_title); ?></option>
						<?php
					}
				}
				// If wishlist page doesn't exist, add it as the first option
				if (!$tesw_wishlist_page_exists) {
					$tesw_wishlist_page = get_page_by_path('wishlist');
					if ($tesw_wishlist_page) {
						?>
						<option value="<?php echo esc_attr($tesw_wishlist_page->ID); ?>" selected="selected"><?php echo esc_html($tesw_wishlist_page->post_title); ?></option>
						<?php
					}
				}

				// Show the rest of the pages
				foreach ($tesw_pages as $tesw_page) {
					if ($tesw_page->post_name !== 'wishlist') {
						$tesw_selected = ($tesw_wishlist_page == $tesw_page->ID) ? 'selected="selected"' : '';
						?>
						<option value="<?php echo esc_attr($tesw_page->ID); ?>" <?php echo esc_attr($tesw_selected); ?>><?php echo esc_html($tesw_page->post_title); ?></option>
						<?php
					}
				}
				?>
			</select>
			<?php
		} else {
			?>
			<p>
				<?php echo esc_html__('No pages found.', 'smart-wishlist-for-woocommerce'); ?>
			</p>
			<?php
		}
		?>
		<p class="tesw-description">
			<?php echo sprintf(
				esc_html__('If you want to create a new page, %s and add the Shortcode [tesw_smart_wishlist].', 'smart-wishlist-for-woocommerce'),
				'<a href="' . esc_url(admin_url('post-new.php?post_type=page')) . '">' . esc_html__('Click Here', 'smart-wishlist-for-woocommerce') . '</a>'
			); ?>
		</p>
		<?php
		// Check if the wishlist page is active by default and show it first
		if (empty($tesw_wishlist_page)) {
			$tesw_pages = get_pages();

			if (!empty($tesw_pages)) {
				$tesw_default_page = $tesw_pages[0];
				$tesw_default_page_id = $tesw_default_page->ID;
				$tesw_selected_page['tesw_page_show'] = $tesw_default_page_id;
			}
		}
		// Update the option outside the function
		update_option('tesw_general_settings_fields', $tesw_selected_page);
	}

	/**
	 * Callback function for the "Add to Wishlist Options" section.
	 *
	 * This function displays the HTML code for the tesw_add_to_wishlist_options section.
	 * Add your tesw_add_to_wishlist_options section HTML code here.
	 * @since    1.0.0
	 */
	function tesw_add_to_wishlist_options_section_callback()
	{
		return;
	}
	/**
	 * Callback function for the "After Product is Added to Wishlist" setting.
	 *
	 * This function displays the options for what to show after a product is added to the wishlist.
	 * @since    1.0.0
	 *
	 * 
	 */
	function tesw_view_button_type_options_callback()
	{
		$tesw_options = get_option('tesw_add_to_wishlist_options_fields');
		$tesw_selected_option = isset($tesw_options['tesw_view_button_type_options']) ? $tesw_options['tesw_view_button_type_options'] : 'tesw_show_view_wishlist';

		?>
		<form>
			<label><input type="radio" name="tesw_add_to_wishlist_options_fields[tesw_view_button_type_options]"
					value="tesw_show_view_wishlist" <?php checked($tesw_selected_option, 'tesw_show_view_wishlist'); ?>> <?php echo esc_html__('Show "View Wishlist" Button', 'smart-wishlist-for-woocommerce'); ?></label><br>
			<label><input type="radio" name="tesw_add_to_wishlist_options_fields[tesw_view_button_type_options]"
					value="tesw_show_view_wishlist_icon" <?php checked($tesw_selected_option, 'tesw_show_view_wishlist_icon'); ?>> <?php echo esc_html__('Show "View Wishlist" Icon', 'smart-wishlist-for-woocommerce'); ?></label>
		</form>
		<?php
	}
	/**
	 * Callback function for the "View Wishlist Text" setting.
	 *
	 * This function displays an input field to Customise the text displayed for browsing the wishlist.
	 * @since    1.0.0
	 */
	function tesw_view_wishlist_text_callback()
	{
		$tesw_options = get_option('tesw_add_to_wishlist_options_fields');
		$tesw_view_wishlist_text = isset($tesw_options['tesw_view_wishlist_text']) ? sanitize_text_field($tesw_options['tesw_view_wishlist_text']) : '';
		?>
		<input type="text" class="tesw-input-field" name="tesw_add_to_wishlist_options_fields[tesw_view_wishlist_text]"
			value="<?php echo esc_attr($tesw_view_wishlist_text); ?>">
		<?php
	}

	/**
	 * Callback function for the "Add to Wishlist Icon" setting.
	 *
	 * This function displays an input field to enter the icon class or URL for the "Add to Wishlist" button.
	 */
	function tesw_add_to_wishlist_icon_style_callback()
	{
		$tesw_options = get_option('tesw_add_to_wishlist_options_fields');
		$tesw_add_to_wishlist_icon_style = isset($tesw_options['tesw_add_to_wishlist_icon_style']) ? sanitize_text_field($tesw_options['tesw_add_to_wishlist_icon_style']) : '';
		?>
		<input type="text" class="tesw-input-field" name="tesw_add_to_wishlist_options_fields[tesw_add_to_wishlist_icon_style]"
			value="<?php echo esc_attr($tesw_add_to_wishlist_icon_style); ?>"
			placeholder="<?php echo esc_attr('E.g., fa fa-heart'); ?>" />
		<p class="tesw-description">
			<?php echo esc_html__('Enter the Font Awesome class for the icon to be displayed on the "Add to Wishlist" button. Only Font Awesome 4 & 5 classes are allowed (e.g., fa fa-heart, fas fa-heart).', 'smart-wishlist-for-woocommerce'); ?>
		</p>
		<?php
	}

	/**
	 * Callback function for the "Wishlist Page Options" section.
	 *
	 * This function displays the HTML code for the Wishlist Page Options section.
	 *
	 * 
	 */
	function tesw_wishlist_page_options_section_callback()
	{
		
		return;
	}
	/**
	 * Callback function for the "Wishlist Table Columns" field.
	 *
	 * This function displays the HTML code for the Wishlist Table Columns field, 
	 * allowing the user to select which columns to display in the wishlist table.
	 * @since    1.0.0
	 */
	function tesw_wishlist_table_columns_callback()
	{
		$tesw_options = get_option('tesw_wishlist_page_options_fields');
		$tesw_wishlist_columns = isset($tesw_options['tesw_wishlist_table_columns']) ? array_map('sanitize_text_field', (array) $tesw_options['tesw_wishlist_table_columns']) : array();

		$tesw_columns = array(
			'product_variations' => esc_html__('Product Variations', 'smart-wishlist-for-woocommerce'),
			'product_stock' => esc_html__('Product Stock', 'smart-wishlist-for-woocommerce'),
			'date_added' => esc_html__('Date Added', 'smart-wishlist-for-woocommerce'),
		);
		?>
		<fieldset>
			<?php foreach ($tesw_columns as $tesw_column => $tesw_label) {
				$tesw_checked = in_array($tesw_column, $tesw_wishlist_columns) ? 'checked="checked"' : '';
				?>
				<label>
					<input type="checkbox" name="tesw_wishlist_page_options_fields[tesw_wishlist_table_columns][]"
						value="<?php echo esc_attr($tesw_column); ?>" <?php echo esc_html($tesw_checked); ?>>
					<?php echo esc_html($tesw_label); ?>
				</label><br>
			<?php } ?>
		</fieldset>
		<?php
	}

	/**
	 * Callback function for the Social Networks Settings section.
	 *
	 * This function displays the description or additional content for the Social Networks Settings section in the WordPress admin panel.
	 * @since    1.0.0
	 *
	 */
	function tesw_social_networks_settings_section_callback()
	{
		
		return;
	}
	/**
	 * Callback function for the "Share Wishlist" field.
	 *
	 * This function displays the HTML code for the Share Wishlist field, allowing the user to enable or disable the sharing of the wishlist through social media platforms.
	 * @since    1.0.0
	 *
	 * 
	 */
	function tesw_enable_social_sharing_field_callback()
	{
		$tesw_settings = get_option('tesw_social_networks_settings_fields');
		$tesw_enable_social_sharing = isset($tesw_settings['tesw_enable_social_sharing']) ? sanitize_text_field($tesw_settings['tesw_enable_social_sharing']) : '0';
		?>
		<div class="tesw-toggle-switch">
			<input type="checkbox" id="tesw_enable_social_sharing"
				name="tesw_social_networks_settings_fields[tesw_enable_social_sharing]" value="1" <?php checked('1', $tesw_enable_social_sharing); ?>>
			<label for="tesw_enable_social_sharing" class="tesw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Share on Social Media" field.
	 *
	 * This function displays the HTML code for the Share on Social Media field, allowing the user to select which social media platforms to include in the wishlist sharing options.
	 * @since    1.0.0
	 *
	 * 
	 */
	function tesw_social_networks_field()
	{
		$tesw_settings = get_option('tesw_social_networks_settings_fields');
		$tesw_social_networks = isset($tesw_settings['tesw_social_networks']) ? array_map('sanitize_text_field', $tesw_settings['tesw_social_networks']) : array();

		$tesw_social_networks_list = array(
			'whatsapp' => esc_html__('WhatsApp', 'smart-wishlist-for-woocommerce'),
			'gmail' => esc_html__('Gmail', 'smart-wishlist-for-woocommerce'),
			'pinterest' => esc_html__('Pinterest', 'smart-wishlist-for-woocommerce'),
			'twitter' => esc_html__('Twitter', 'smart-wishlist-for-woocommerce'),
			'copy_link' => esc_html__('Copy Link', 'smart-wishlist-for-woocommerce'),
		);

		foreach ($tesw_social_networks_list as $tesw_key => $tesw_label) {
			$tesw_checked = in_array($tesw_key, $tesw_social_networks) ? 'checked="checked"' : '';
			?>
			<input type="checkbox" id="<?php echo esc_attr($tesw_key); ?>"
				name="tesw_social_networks_settings_fields[tesw_social_networks][]" value="<?php echo esc_attr($tesw_key); ?>" <?php echo esc_attr($tesw_checked); ?> />
			<label for="<?php echo esc_attr($tesw_key); ?>"><?php echo esc_html($tesw_label); ?></label><br>
			<?php
		}
	}

	/**
	 * Callback function for the "Social Networks Icon" field.
	 *
	 * This function displays the HTML code for the Social Networks Icon field, allowing the user to enable or disable the display of icons for social networks.
	 *
	 * @since 1.0.0
	 */
	function tesw_social_networks_icon()
	{
		$tesw_settings = get_option('tesw_social_networks_settings_fields');
		$tesw_social_networks_show_icon = isset($tesw_settings['tesw_social_networks_show_icon']) ? sanitize_text_field($tesw_settings['tesw_social_networks_show_icon']) : 0;
		?>
		<div class="tesw-toggle-switch">
			<input type="checkbox" id="tesw_social_networks_show_icon"
				name="tesw_social_networks_settings_fields[tesw_social_networks_show_icon]" value="1" <?php checked(1, $tesw_social_networks_show_icon); ?>>
			<label for="tesw_social_networks_show_icon" class="tesw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Style Settings" section.
	 *
	 * This function displays the Style Settings section in the plugin's settings page.
	 *
	 * @since 1.0.0
	 */
	function tesw_style_settings_section_callback()
	{
		
		return;
	}

	/**
	 * Callback function for the "Enable CSS Styles" field.
	 *
	 * This function displays a checkbox field allowing the user to enable or disable CSS styles.
	 *
	 * @since 1.0.0
	 */
	function tesw_enable_css_style_field_callback()
	{
		$tesw_options = get_option('tesw_style_options');
		$tesw_enable_css = isset($tesw_options['tesw_enable_css']) ? sanitize_text_field($tesw_options['tesw_enable_css']) : '';
		?>
		<div class="tesw-toggle-switch">
			<input type="checkbox" id="tesw_enable_css" name="tesw_style_options[tesw_enable_css]" value="1" <?php echo checked(1, $tesw_enable_css, false); ?>>
			<label for="tesw_enable_css" class="tesw-toggle-slider"></label>
		</div>
		<?php
	}

	/**
	 * Callback function for the "Button Radius" field.
	 *
	 * This function displays the HTML code for the Button Radius field, allowing the user to select the desired button radius.
	 *
	 * @since 1.0.0
	 */
	function tesw_button_radius_css_callback()
	{
		// Retrieve the saved values from the database
		$tesw_options = get_option('tesw_style_options');

		// Sanitize the saved value
		$tesw_button_radius = isset($tesw_options['tesw_button_radius']) ? sanitize_text_field($tesw_options['tesw_button_radius']) : '';

		// Input Text Field for Button Radius
		?>
		<input type="text" class="tesw-input-field" id="tesw_button_radius" name="tesw_style_options[tesw_button_radius]"
			value="<?php echo esc_attr($tesw_button_radius); ?>" />
		<?php
	}

	/**
	 * Callback function for the "Button Color" field.
	 *
	 * This function displays the HTML code for the Button Color field, allowing the user to select the desired button color.
	 *
	 * @since 1.0.0
	 */
	function tesw_button_color_css_callback()
	{
		// Retrieve the saved values from the database
		$tesw_options = get_option('tesw_style_options');

		// Sanitize the saved value
		$tesw_button_color = isset($tesw_options['tesw_button_color']) ? sanitize_hex_color($tesw_options['tesw_button_color']) : '';

		// Checkbox value for disabling button color change
		$tesw_button_color_enable = isset($tesw_options['tesw_button_color_enable']) ? $tesw_options['tesw_button_color_enable'] : '';

		// Input Field with CSS Color Picker for Button Color
		?>
		<label for="tesw_button_color_enable">
			<input type="checkbox" id="tesw_button_color_enable" name="tesw_style_options[tesw_button_color_enable]" value="1"
				<?php checked(1, $tesw_button_color_enable); ?> />
			<?php esc_html_e('Enable Button Color Change', 'smart-wishlist-for-woocommerce'); ?>
		</label>
		<br />
		<br />
		<input type="color" class="tesw-input-field" id="tesw_button_color" name="tesw_style_options[tesw_button_color]"
			value="<?php echo esc_attr($tesw_button_color); ?>" <?php echo ($tesw_button_color_enable == 1) ? '' : ' '; ?> />
		<?php
	}

	/**
	 * Callback function for the "Text Color" CSS field.
	 *
	 * This function displays the HTML code for the Text Color CSS input field.
	 *
	 * @since    1.0.0
	 */
	function tesw_text_color_css_callback()
	{
		$tesw_options = get_option('tesw_style_options');
		$tesw_text_color_css = isset($tesw_options['tesw_text_color_css']) ? $tesw_options['tesw_text_color_css'] : '';
		$tesw_text_color_enable = isset($tesw_options['tesw_text_color_enable']) ? $tesw_options['tesw_text_color_enable'] : '';

		?>
		<label for="tesw_text_color_enable">
			<input type="checkbox" id="tesw_text_color_enable" name="tesw_style_options[tesw_text_color_enable]" value="1" <?php checked(1, $tesw_text_color_enable); ?> />
			<?php esc_html_e('Enable Text Color Change', 'smart-wishlist-for-woocommerce'); ?>
		</label>
		<br/>
		<br/>
		<input type="color" class="tesw-input-field" id="tesw_text_color_css" name="tesw_style_options[tesw_text_color_css]"
			value="<?php echo esc_attr($tesw_text_color_css); ?>" <?php echo ($tesw_text_color_enable == 1) ? '' : ''; ?> />
		<?php
	}

	/**
	 * Callback function for the "Button Text Size" field.
	 *
	 * This function displays the HTML code for the Button Text Size field, allowing the user to select the desired button text size.
	 *
	 * @since 1.0.0
	 */
	function tesw_button_text_size_css_callback()
	{
		// Retrieve the saved values from the database
		$tesw_options = get_option('tesw_style_options');

		// Sanitize the saved value
		$tesw_button_text_size = isset($tesw_options['tesw_button_text_size']) ? sanitize_text_field($tesw_options['tesw_button_text_size']) : '';

		// Input Text Field for Button Text Size
		?>
		<input type="text" class="tesw-input-field" id="tesw_button_text_size" name="tesw_style_options[tesw_button_text_size]"
			value="<?php echo esc_attr($tesw_button_text_size); ?>" />
		<?php
	}

	/**
	 * Callback function for the "Button Text Style" field.
	 *
	 * This function displays the HTML code for the Button Text Style field, allowing the user to select the desired font style for the button text.
	 *
	 * @since 1.0.0
	 */
	function tesw_button_text_style_css_callback()
	{
		// Retrieve the saved values from the database
		$tesw_options = get_option('tesw_style_options');

		// Define the font style options
		$tesw_font_style_options = array(
			'normal' => esc_html__('Normal', 'smart-wishlist-for-woocommerce'),
			'italic' => esc_html__('Italic', 'smart-wishlist-for-woocommerce'),
			'oblique' => esc_html__('Oblique', 'smart-wishlist-for-woocommerce'),
		);

		// Sanitize and validate the saved option value
		$tesw_button_text_style = isset($tesw_options['tesw_button_text_style']) ? sanitize_text_field($tesw_options['tesw_button_text_style']) : '';

		// Select Field for Font Style
		?>
		<select class="tesw-input-field" id="tesw_button_text_style" name="tesw_style_options[tesw_button_text_style]">
			<?php foreach ($tesw_font_style_options as $tesw_value => $tesw_label): ?>
				<?php $tesw_selected = ($tesw_button_text_style == $tesw_value) ? 'selected="selected"' : ''; ?>
				<option value="<?php echo esc_attr($tesw_value); ?>" <?php echo esc_attr($tesw_selected); ?>><?php echo esc_html($tesw_label); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	
	/**
	 * Displays the plugin settings page.
	 *
	 * This function generates the HTML markup for the plugin settings page.
	 * It includes a welcome message, navigation tabs, and forms for each
	 * settings section based on the selected tab.
	 *
	 * @since 1.0.0
	 */
	function tesw_wishlist_plugin_settings_page()
	{
		if (!current_user_can('manage_options')) {
			return;
		}
		
		// Determine the active tab based on the URL parameter
		$tesw_active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'tesw-general';
		?>
		<div id="tesw-plugin-container">
			<div class="tesw-mode">
				<div class="tesw-container">
					<div class="tesw-welcome-container">
						<h1 class="tesw-welcome-title">
							<span class="tesw-animated-text ">
								<?php echo esc_html__('Welcome to Smart Wishlist for WooCommerce', 'smart-wishlist-for-woocommerce'); ?>
							</span>
						</h1>
						<p class="tesw-welcome-message">
							<?php echo esc_html__('Thank you for using the Smart Wishlist for WooCommerce plugin. This is the plugin settings page.', 'smart-wishlist-for-woocommerce'); ?>
						</p>
						<div class="tesw-wrap">
							<h1 class="tesw-wishlist-h1 tesw-neon-text">
								<?php echo esc_html__('Smart Wishlist Settings', 'smart-wishlist-for-woocommerce'); ?>
							</h1>
						</div>
					</div>
				</div>
				<br>
				<div class="tesw-toggle-description">
					<div class="tesw-toggle-switch-mode">
						<input id="tesw-toggle" class="tesw-toggle-input" type="checkbox">
						<label for="tesw-toggle" class="tesw-toggle-label"></label>
					</div>
					<h4 class="tesw-toggle">
						<?php esc_html_e('Dark Mode', 'smart-wishlist-for-woocommerce'); ?>
					</h4>
				</div>
				<?php
				// Display a success message if the settings were updated
				if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
					?>
					<div class="tesw-notice notice tesw-notice-success notice-success tesw-is-dismissible is-dismissible">
						<p>
							<?php echo esc_html__('Your changes have been saved.', 'smart-wishlist-for-woocommerce'); ?>
						</p>
					</div>
					<?php
				}
				?>
				<div class="tesw-nav-wrapper nav-tab-wrapper">
					<!-- General Settings tab -->
					<a href="?page=tesw-wishlist-plugin&tab=tesw-general"
						class="tesw-nav-tab nav-tab <?php echo esc_attr(($tesw_active_tab === 'tesw-general' || !isset($_GET['tab'])) ? 'tesw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('General Settings', 'smart-wishlist-for-woocommerce'); ?></a>

					<!-- Add to Wishlist Options tab -->
					<a href="?page=tesw-wishlist-plugin&tab=tesw-add-options"
						class="tesw-nav-tab nav-tab <?php echo esc_attr(($tesw_active_tab === 'tesw-add-options') ? 'tesw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('Add to Wishlist Options', 'smart-wishlist-for-woocommerce'); ?></a>

					<!-- Wishlist Page Options tab -->
					<a href="?page=tesw-wishlist-plugin&tab=tesw-wishlist-page"
						class="tesw-nav-tab nav-tab <?php echo esc_attr(($tesw_active_tab === 'tesw-wishlist-page') ? 'tesw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('Wishlist Page Options', 'smart-wishlist-for-woocommerce'); ?></a>

					<!-- Social Networks tab -->
					<a href="?page=tesw-wishlist-plugin&tab=tesw-social-page"
						class="tesw-nav-tab nav-tab <?php echo esc_attr(($tesw_active_tab === 'tesw-social-page') ? 'tesw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('Social Networks', 'smart-wishlist-for-woocommerce'); ?></a>

					<!-- CSS Style Features tab -->
					<a href="?page=tesw-wishlist-plugin&tab=tesw-css-style-page"
						class="tesw-nav-tab nav-tab <?php echo esc_attr(($tesw_active_tab === 'tesw-css-style-page') ? 'tesw-nav-tab nav-tab-active' : ''); ?>">
						<?php echo esc_html__('CSS Style Options', 'smart-wishlist-for-woocommerce'); ?></a>				
				</div>
				<?php
				// Display the appropriate form based on the active tab
				switch ($tesw_active_tab) {
					case 'tesw-general':
						?>
						<!-- General Settings form -->
						<form method="post" action="options.php" id="tesw-form-css">
							<?php
							settings_fields('tesw_general_settings');
							do_settings_sections('tesw_general_settings');
							wp_nonce_field('tesw_general_settings_nonce', 'tesw_general_settings_nonce');
							submit_button(esc_html__('Save Changes', 'smart-wishlist-for-woocommerce'), 'tesw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'tesw-add-options':
						?>
						<!-- Add to Wishlist Options form -->
						<form method="post" action="options.php" id="tesw-form-css">
							<?php
							settings_fields('tesw_add_to_wishlist_options');
							do_settings_sections('tesw_add_to_wishlist_options');
							wp_nonce_field('tesw_add_to_wishlist_options_nonce', 'tesw_add_to_wishlist_options_nonce');
							submit_button(esc_html__('Save Changes', 'smart-wishlist-for-woocommerce'), 'tesw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'tesw-wishlist-page':
						?>
						<!-- Wishlist Page Options form -->
						<form method="post" action="options.php" id="tesw-form-css">
							<?php
							settings_fields('tesw_wishlist_page_options');
							do_settings_sections('tesw_wishlist_page_options');
							wp_nonce_field('tesw_wishlist_page_options_nonce', 'tesw_wishlist_page_options_nonce');
							submit_button(esc_html__('Save Changes', 'smart-wishlist-for-woocommerce'), 'tesw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'tesw-social-page':
						?>
						<!-- Social Networks form -->
						<form method="post" action="options.php" id="tesw-form-css">
							<?php
							settings_fields('tesw_social_networks_settings');
							do_settings_sections('tesw_social_networks_settings');
							wp_nonce_field('tesw_social_networks_settings_nonce', 'tesw_social_networks_settings_wpnonce');
							submit_button(esc_html__('Save Changes', 'smart-wishlist-for-woocommerce'), 'tesw-submit-button',  false);
							?>
						</form>
						<?php
						break;
					case 'tesw-css-style-page':
						?>
						<!-- CSS Style Features form -->
						<form method="post" action="options.php" id="tesw-form-css">
							<?php
							settings_fields('tesw_style_settings');
							do_settings_sections('tesw_style_settings');
							wp_nonce_field('tesw_style_settings_nonce', 'tesw_style_settings_nonce');
							submit_button(esc_html__('Save Changes', 'smart-wishlist-for-woocommerce'), 'tesw-submit-button', false);
							?>
						</form>
						<?php
						break;
					default:
						// Default behavior when the tab is not recognized
						echo esc_html__('Invalid tab selection.', 'smart-wishlist-for-woocommerce');
						break;
				}
				?>
			</div>
		</div>
		<?php
	}
}