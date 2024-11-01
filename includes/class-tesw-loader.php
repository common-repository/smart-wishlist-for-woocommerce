<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://tekniskera.com/
 * @since      1.0.0
 *
 * @package    Tesw
 * @subpackage Tesw/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Tesw
 * @subpackage Tesw/includes
 * @author     Teknisk Era <admin@tekniskera.com>
 */
class Tesw_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $tesw_actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $tesw_actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $tesw_filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $tesw_filters;

	/**
	 * The array of shortcode registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $tesw_shortcodes    The shortcode registered with WordPress to fire when the plugin loads.
	 */
	protected $tesw_shortcodes;

	/**
	 * Initialize the collections used to maintain the actions, filters and shortcode.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->tesw_actions = array();
		$this->tesw_filters = array();
		$this->tesw_shortcodes =array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $tesw_hook             The name of the WordPress action that is being registered.
	 * @param    object               $tesw_component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $tesw_callback         The name of the function definition on the $component.
	 * @param    int                  $tesw_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $tesw_accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function tesw_add_action( $tesw_hook, $tesw_component, $tesw_callback, $tesw_priority = 10, $tesw_accepted_args = 1 ) {
		$this->tesw_actions = $this->tesw_add( $this->tesw_actions, $tesw_hook, $tesw_component, $tesw_callback, $tesw_priority, $tesw_accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $tesw_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $tesw_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $tesw_callback         The name of the function definition on the $component.
	 * @param    int                  $tesw_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $tesw_accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function tesw_add_filter( $tesw_hook, $tesw_component, $tesw_callback, $tesw_priority = 10, $tesw_accepted_args = 1 ) {
		$this->tesw_filters = $this->tesw_add( $this->tesw_filters, $tesw_hook, $tesw_component, $tesw_callback, $tesw_priority, $tesw_accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $tesw_hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $tesw_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $tesw_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $tesw_callback         The name of the function definition on the $component.
	 * @param    int                  $tesw_priority         The priority at which the function should be fired.
	 * @param    int                  $tesw_accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function tesw_add( $tesw_hooks, $tesw_hook, $tesw_component, $tesw_callback, $tesw_priority, $tesw_accepted_args ) {

		$tesw_hooks[] = array(
			'hook'          => $tesw_hook,
			'component'     => $tesw_component,
			'callback'      => $tesw_callback,
			'priority'      => $tesw_priority,
			'accepted_args' => $tesw_accepted_args
		);

		return $tesw_hooks;
	}

	/**
	* Add a new shortcode to the collection to be registered with WordPress.
	*
	* @since    1.0.0
	* @param    string   $tesw_shortcode   The name of the shortcode being registered.
	* @param    object   $tesw_component   A reference to the instance of the object on which the shortcode is defined.
	* @param    string   $tesw_callback    The name of the function definition on the $tesw_component.
	*/
	public function tesw_add_shortcode( $tesw_shortcode, $tesw_component, $tesw_callback ) {
		$this->tesw_shortcodes = $this->tesw_add_shortcode_internal( $this->tesw_shortcodes, $tesw_shortcode, $tesw_component, $tesw_callback );
	}
	
	/**
	 * A utility function that is used to register shortcodes into a single collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $tesw_shortcodes       The collection of shortcodes that is being registered.
	 * @param    string               $tesw_shortcode        The name of the shortcode being registered.
	 * @param    object               $tesw_component        A reference to the instance of the object on which the shortcode is defined.
	 * @param    string               $tesw_callback         The name of the function definition on the $component.
	 * @return   array                                           The collection of shortcodes registered.
	 */
	private function tesw_add_shortcode_internal( $tesw_shortcodes, $tesw_shortcode, $tesw_component, $tesw_callback ) {
		$tesw_shortcodes[] = array(
			'shortcode'   => $tesw_shortcode,
			'component'   => $tesw_component,
			'callback'    => $tesw_callback,
		);
	
		return $tesw_shortcodes;
	}
	/**
	 * Register the filters, actions and shortcode with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function tesw_run() {

		foreach ( $this->tesw_filters as $tesw_hook ) {
			add_filter( $tesw_hook['hook'], array( $tesw_hook['component'], $tesw_hook['callback'] ), $tesw_hook['priority'], $tesw_hook['accepted_args'] );
		}

		foreach ( $this->tesw_actions as $tesw_hook ) {
			add_action( $tesw_hook['hook'], array( $tesw_hook['component'], $tesw_hook['callback'] ), $tesw_hook['priority'], $tesw_hook['accepted_args'] );
		}
		foreach ( $this->tesw_shortcodes as $tesw_shortcode ) {
			add_shortcode( $tesw_shortcode['shortcode'], array( $tesw_shortcode['component'], $tesw_shortcode['callback'] ) );
		}
	}

}
