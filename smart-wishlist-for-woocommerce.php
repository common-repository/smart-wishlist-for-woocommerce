<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tekniskera.com/
 * @since             1.0.0
 * @package           Tesw
 *
 * @wordpress-plugin
 * Plugin Name:       Smart Wishlist For WooCommerce
 * Plugin URI:        https://tekniskera.com/docs/smart-wishlist-for-woocommerce/
 * Description:       The Smart Wishlist Plugin is an extension designed specifically for WooCommerce. It comes with a range of features and is user-friendly. The plugin enhances the shopping experience by allowing users to create and manage wishlists of their favorite products.
 * Version:           1.0.1
 * Author:            Teknisk Era
 * Author URI:        https://tekniskera.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smart-wishlist-for-woocommerce
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    // If this file is called directly, abort.
    die;
}

define('TESW_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tesw-activator.php
 */
function tesw_activate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-tesw-activator.php';
    Tesw_Activator::tesw_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tesw-deactivator.php
 */
function tesw_deactivate()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-tesw-deactivator.php';
    Tesw_Deactivator::tesw_deactivate();
}
register_activation_hook(__FILE__, 'tesw_activate');
register_deactivation_hook(__FILE__, 'tesw_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (is_plugin_active('woocommerce/woocommerce.php')) {
    
    // WooCommerce plugin is active Include the main plugin class file
    // Call the main plugin function
    require plugin_dir_path(__FILE__) . 'includes/class-tesw.php';

    tesw_run();
    
} else {

    // WooCommerce plugin is not active Add an action hook to display an admin notice
    add_action('admin_notices', 'tesw_activate_notification');
}

/**
 * Function to display an activation notification and deactivate the plugin.
 */
function tesw_activate_notification()
{
    // Output an error message in the admin area
    ?>
    <div class="tesw-error error">
        <p>
            <?php esc_html_e('Smart Wishlist For WooCommerce plugin is not activated. It requires WooCommerce to work.', 'smart-wishlist-for-woocommerce'); ?>
        </p>
    </div>
    <?php
    // Deactivate the plugin
    deactivate_plugins(plugin_basename(__FILE__));
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

 function tesw_run()
 {
    if (function_exists('is_multisite') && is_multisite())
    {
        // Check if the pro version of the plugin is active
        if (is_plugin_active('smart-wishlist-pro-version-for-woocommerce/smart-wishlist-pro-version-for-woocommerce.php'))
        {
            return; // Pro version is active, so don't load the free version
        }
    }
    else
    {
        // Check if the pro version of the plugin is active
        if (in_array('smart-wishlist-pro-version-for-woocommerce/smart-wishlist-pro-version-for-woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
        {
            return; // Pro version is active, so don't load the free version
        }
    }

    // Load and run the free version of the plugin
    $tesw_plugin = new Tesw();
    $tesw_plugin->tesw_run();
}
 
