<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tesw
 * @subpackage Tesw/includes
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tesw_Loader    $tesw_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $tesw_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $tesw_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $tesw_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $tesw_version    The current version of the plugin.
	 */
	protected $tesw_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('TESW_VERSION')) {
			$this->tesw_version = TESW_VERSION;
		} else {
			$this->tesw_version = '1.0.0';
		}
		$this->tesw_plugin_name = 'smart-wishlist-for-woocommerce';

		$this->tesw_load_dependencies();
		$this->tesw_set_locale();
		$this->tesw_define_admin_hooks();
		$this->tesw_define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tesw_Loader. Orchestrates the hooks of the plugin.
	 * - Tesw_i18n. Defines internationalization functionality.
	 * - Tesw_Admin. Defines all hooks for the admin area.
	 * - Tesw_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function tesw_load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tesw-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tesw-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-tesw-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-tesw-public.php';

		$this->tesw_loader = new Tesw_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tesw_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function tesw_set_locale()
	{

		$tesw_plugin_i18n = new Tesw_i18n();

		$this->tesw_loader->tesw_add_action('plugins_loaded', $tesw_plugin_i18n, 'tesw_load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function tesw_define_admin_hooks()
	{
		$tesw_plugin_admin = new Tesw_Admin($this->tesw_get_plugin_name(), $this->tesw_get_version());

		$this->tesw_loader->tesw_add_action('admin_enqueue_scripts', $tesw_plugin_admin, 'tesw_enqueue_styles');
		$this->tesw_loader->tesw_add_action('admin_enqueue_scripts', $tesw_plugin_admin, 'tesw_enqueue_scripts');
		$this->tesw_loader->tesw_add_action('admin_menu', $tesw_plugin_admin, 'tesw_wishlist_plugin_add_menu_page');
		$this->tesw_loader->tesw_add_action('admin_init', $tesw_plugin_admin, 'tesw_register_settings');
		$this->tesw_loader->tesw_add_action('admin_menu', $tesw_plugin_admin, 'tesw_add_custom_menu_class');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function tesw_define_public_hooks()
	{
		$tesw_plugin_public = new Tesw_Public($this->tesw_get_plugin_name(), $this->tesw_get_version());

		$this->tesw_loader->tesw_add_action('wp_enqueue_scripts', $tesw_plugin_public, 'tesw_enqueue_styles');
		$this->tesw_loader->tesw_add_action('wp_enqueue_scripts', $tesw_plugin_public, 'tesw_enqueue_scripts');
		$this->tesw_loader->tesw_add_action('wp_enqueue_scripts', $tesw_plugin_public, 'tesw_change_button_css_style');
		$this->tesw_loader->tesw_add_action('woocommerce_after_shop_loop_item', $tesw_plugin_public, 'tesw_wishlistify_add_wishlist_button', 15);
		$this->tesw_loader->tesw_add_action('woocommerce_after_add_to_cart_button', $tesw_plugin_public, 'tesw_wishlistify_add_wishlist_button');
		$this->tesw_loader->tesw_add_action('wp_ajax_tesw_add_to_wishlist', $tesw_plugin_public, 'tesw_add_to_wishlist');
		$this->tesw_loader->tesw_add_action('wp_ajax_nopriv_tesw_add_to_wishlist', $tesw_plugin_public, 'tesw_add_to_wishlist');
		$this->tesw_loader->tesw_add_action('wp_ajax_tesw_remove_product_from_wishlist', $tesw_plugin_public, 'tesw_remove_product_from_wishlist');
		$this->tesw_loader->tesw_add_action('wp_ajax_nopriv_tesw_remove_product_from_wishlist', $tesw_plugin_public, 'tesw_remove_product_from_wishlist');
		$this->tesw_loader->tesw_add_action('wp_ajax_tesw_add_multiple_to_cart', $tesw_plugin_public, 'tesw_add_multiple_to_cart');
		$this->tesw_loader->tesw_add_action('wp_ajax_nopriv_tesw_add_multiple_to_cart', $tesw_plugin_public, 'tesw_add_multiple_to_cart');
		$this->tesw_loader->tesw_add_action('wp_ajax_tesw_remove_multiple_products_from_wishlist', $tesw_plugin_public, 'tesw_remove_multiple_products_from_wishlist');
		$this->tesw_loader->tesw_add_action('wp_ajax_nopriv_tesw_remove_multiple_products_from_wishlist', $tesw_plugin_public, 'tesw_remove_multiple_products_from_wishlist');
		$this->tesw_loader->tesw_add_shortcode('tesw_smart_wishlist',$tesw_plugin_public, 'tesw_wishlist_product_shortcode');

	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function tesw_run()
	{
		$this->tesw_loader->tesw_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function tesw_get_plugin_name()
	{
		return $this->tesw_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tesw_Loader    Orchestrates the hooks of the plugin.
	 */
	public function tesw_get_loader()
	{
		return $this->tesw_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function tesw_get_version()
	{
		return $this->tesw_version;
	}

}