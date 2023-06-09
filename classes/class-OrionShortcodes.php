<?php

/**
 * Class OrionShortcodes
 *
 * Handles the shortcodes
 *
 * @package WordPress
 * @subpackage Orionplugin
 * @since Orionplugin 1.0.0
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) die('Bad dog. No biscuit!');

/**
 * Class to handle shortcodes
 */
class OrionShortcodes
{
    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * Returns an instance of this class.
     */
    public static function get_instance()
    {

        if (null == self::$instance) {
            self::$instance = new OrionShortcodes();
        }

        return self::$instance;
    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
        // Add shortcode
        add_shortcode(
            'employees_list',
            array($this, 'orion_employees_list_shortcode_handler')
        );
    }

    /**
     * Register shortcode employees_list
     */
    public function orion_employees_list_shortcode_handler($atts)
    {
        global $wpdb;

        // Only in shortcode page insert
        OrionShortcodes::orion_enqueue_front_scripts();

        $default = array(
            'slider'     => false,
            'column'     => 4,
            'categories' => 'all'
        );

        $a = shortcode_atts($default, $atts);

        $coaches = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider'");


        ob_start();

        echo 'listado de coaches';
        echo '<pre>';
        var_dump($coaches);
        echo '</pre>';

        $output = ob_get_clean();

        return $output;
    }

    /**
     * Enqueue front scripts
     *
     * @return void
     */
    public static function orion_enqueue_front_scripts()
    {
        wp_enqueue_style('orion-shortcode-styles', ORION_PLUGIN_DIR_URL . "assets/css/shortcode.css", array(), ORION_VERSION);
        wp_enqueue_script('orion-shortcode-script', ORION_PLUGIN_DIR_URL . "assets/js/shortcode.js", array(), ORION_VERSION);
    }
}
