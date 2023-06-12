<?php

/**
 * Orionplugin
 *
 * @subpackage        WordPress
 * @package           Orionplugin
 * @author            Jose Lazo
 * @copyright         2023 Jose Lazo
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Orion Plugin
 * Plugin URI:        https://github.com/jjlazo79/orion-plugin
 * Description:       Plugin de funciones para Orion
 * Version:           1.1.2
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Jose Lazo
 * Author URI:        http://www.joselazo.es/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       orionplugin
 * Domain Path:       /languages
 */

// Exit if accessed directly
defined('ABSPATH') or die('Bad dog. No biscuit!');
// Define some constants plugin
define('ORION_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('ORION_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('ORION_VERSION', '1.1.2');
define('ORION_TEXT_DOMAIN', 'orionplugin');

// Activation, deactivation and uninstall plugin hooks
register_activation_hook(__FILE__, array('OrionPlugin', 'orion_plugin_activation'));
register_deactivation_hook(__FILE__,  array('OrionPlugin',  'orion_plugin_deactivation'));
register_uninstall_hook(__FILE__,  array('OrionPlugin',  'orion_plugin_uninstall'));

// Initialize the plugin
$orion_plugin = new OrionPlugin();

class OrionPlugin
{

	/**
	 * Initializes the plugin.
	 *
	 * To keep the initialization fast, only add filter and action
	 * hooks in the constructor.
	 */
	public function __construct()
	{
		// Include classes
		include_once 'classes/class-OrionShortcodes.php';
		include_once 'classes/class-OrionFunctions.php';
		include_once 'classes/class-OrionTaxonomies.php';
		//Actions
		add_action('init', array($this, 'orion_localize_scripts'));
		add_action('plugins_loaded', array('OrionShortcodes', 'get_instance'));
		add_action('plugins_loaded', array('OrionFunctions', 'get_instance'));
		add_action('plugins_loaded', array('OrionTaxonomies', 'get_instance'));
	}

	/**
	 * Activation hook
	 *
	 * @return void
	 */
	public static function orion_plugin_activation()
	{
		if (!current_user_can('activate_plugins')) return;

		// Add new column
		if (in_array('ameliabooking/ameliabooking.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			$new_column = OrionPlugin::add_new_column();
		}

		// Clear the permalinks after the post type has been registered.
		flush_rewrite_rules();
		add_option('Orion_plugin_do_activation_redirect', true);
	}

	/**
	 * Deactivation hook
	 *
	 * @return void
	 */
	public static function orion_plugin_deactivation()
	{
		// Delete options
		delete_option('Orion_plugin_do_activation_redirect');

		// Unregister the post type and taxonomies, so the rules are no longer in memory.
		// unregister_post_type('personas');

		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();
	}

	/**
	 * Unistall hook
	 *
	 * @return void
	 */
	public static function orion_plugin_uninstall()
	{
		// Delete options
		delete_option('Orion_plugin_do_activation_redirect');
		// Drop plugin column
		$drop_column = OrionPlugin::drop_new_column();
	}

	/**
	 * Add column to data base
	 *
	 * @return void
	 */
	public static function add_new_column()
	{
		global $wpdb;
		global $jal_db_version;

		add_option('jal_db_version', $jal_db_version);

		$table_name = $wpdb->prefix . 'amelia_users';
		$row        = $wpdb->get_results("SELECT COLUMN_NAME
		FROM INFORMATION_SCHEMA.COLUMNS
		WHERE TABLE_NAME = '$table_name'
		AND COLUMN_NAME = 'coach_category_slug'");

		if (empty($row)) {
			$wpdb->query("ALTER TABLE `$table_name` ADD coach_category_slug VARCHAR(255) NOT NULL");
		}
	}

	/**
	 * Drop plugin column
	 *
	 * @return void
	 */
	public static function drop_new_column()
	{
		global $wpdb;
		// $table_name = $wpdb->prefix . 'amelia_users';
		// $sql = "DROP TABLE IF EXISTS $table_name";
		// $wpdb->query($sql);

		$ameliausers_table_name = $wpdb->prefix . 'amelia_users';
		$row                    = $wpdb->get_results("SELECT COLUMN_NAME
		FROM INFORMATION_SCHEMA.COLUMNS
		WHERE TABLE_NAME = '$ameliausers_table_name'
		AND COLUMN_NAME = 'coach_category_slug'");

		if (!empty($row)) {
			$wpdb->query("ALTER TABLE `$ameliausers_table_name` DROP COLUMN coach_category_slug");
		}
		delete_option('jal_db_version');
	}

	/**
	 * Localize path folder
	 *
	 * @return void
	 */
	public function orion_localize_scripts()
	{
		$domain = ORION_TEXT_DOMAIN;
		$locale = apply_filters('plugin_locale', get_locale(), $domain);
		load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages');
	}
}
