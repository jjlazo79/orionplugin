<?php

declare(strict_types=1);
/**
 * Class OrionFunctions
 *
 * Add new functions and addons
 * @package WordPress
 * @subpackage Orionplugin
 * @since Orionplugin 1.0.0
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) die('Bad dog. No biscuit!');

/**
 * The main OrionFunctions class
 */
class OrionFunctions
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
            self::$instance = new OrionFunctions();
        }

        return self::$instance;
    }


    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
        // Actions
        add_action('wp_loaded', array($this, 'buffer_start'));
        add_action('wp_enqueue_scripts', array($this, 'orion_plugin_assets'));
        // add_action('admin_menu', array($this, 'orion_add_tipocoach_taxonomy_admin_page'));
        // add_action('show_user_profile', array($this, 'orion_edit_user_tipocoach_section'));
        // add_action('edit_user_profile', array($this, 'orion_edit_user_tipocoach_section'));
        // add_action('user_new_form', array($this, 'orion_edit_user_tipocoach_section'));
        // add_action('personal_options_update', array($this, 'orion_save_user_tipocoach_terms'));
        // add_action('edit_user_profile_update', array($this, 'orion_save_user_tipocoach_terms'));
        // add_action('user_register', array($this, 'orion_save_user_tipocoach_terms'));
        // Filters
        add_filter('nav_menu_link_attributes', array($this, 'obfuscate_specific_menu_links'), 10, 3);
        // add_filter('manage_edit-tipocoach_columns', array($this, 'orion_manage_tipocoach_user_column'));
        // add_filter('manage_users_columns', array($this, 'orion_manage_users_column'));
        // add_filter('manage_tipocoach_custom_column', array($this, 'orion_manage_tipocoach_column'), 10, 3);
        // add_filter('manage_users_custom_column', array($this, 'orion_manage_user_column'), 10, 3);
        // add_filter('sanitize_user', array($this, 'orion_disable_tipocoach_username'));
        // add_filter('parent_file', array($this, 'orion_change_parent_file'));
        // // add_filter('the_author', array($this, 'orion_ameliaemployees_public_profile'));
        // add_filter('pre_user_query', array($this, 'wp_user_query_random_enable'), 1);
        // add_filter('template_include', array($this, 'orion_author_template_loader'), 1);
    }

    /**
     */
    function buffer_start()
    {
        ob_start(array($this, 'akn_ofbuscate_buffer'));
    }

    /**
     */
    function buffer_end()
    {
        ob_end_flush();
    }

    /**
     * Obfuscate some links
     *
     * @param string $buffer
     * @return string
     */
    function akn_ofbuscate_buffer($buffer)
    {
        $result = preg_replace_callback('#<a[^>]+(href=(\"|\')([^\"\']*)(\'|\")[^>]+class=(\"|\')[^\'\"]*obfuscate[^\'\"]*(\"|\')|class=(\"|\')[^\'\"]*obfuscate[^\'\"]*(\"|\')[^>]+href=(\"|\')([^\"\']*)(\'|\"))[^>]*>(.+(?!<a))<\/a>#imUs', function ($matches) {
            preg_match('#<a[^>]+class=[\"|\\\']([^\\\'\"]+)[\"|\\\']#imUs', $matches[0], $matches_classes);
            $classes = trim(preg_replace('/\s+/', ' ', str_replace('obfuscate', '', $matches_classes[1])));
            return '<span class="akn-obf-link' . ($classes ? ' ' . $classes : '') . '" data-o="' . base64_encode($matches[3] ?: $matches[10]) . '" data-b="' . ((strpos(strtolower($matches[0]), '_blank') !== false) ? '1' : '0') . '">' . $matches[12] . '</span>';
        }, $buffer);
        return $result;
    }


    /**
     * Obfuscate some menu's links
     *
     * @param array $atts
     * @param object $item
     * @param object $args
     * @return void
     */
    public function obfuscate_specific_menu_links($atts, $item, $args)
    {
        $menu_items = array(1510, 1);
        if (in_array($item->ID, $menu_items)) {
            $atts['class'] = 'obfuscate';
        }

        return $atts;
    }

    /**
     * Get employed services by userID 
     *
     * @param int $userId
     * @return array
     * ['price']
     * ['serviceId']
     */
    public static function getUserservicesByUserId($userId)
    {
        global $wpdb;
        $services = $wpdb->prepare("SELECT *
        FROM {$wpdb->prefix}amelia_providers_to_services AS US
            JOIN {$wpdb->prefix}amelia_services AS S
            ON US.serviceId = S.id
        WHERE US.userId = %d", $userId);

        return $wpdb->get_results($services);
    }

    /**
     * Get services by serviceID 
     *
     * @param int $userId
     * @return array
     * ['name']
     * ['description']
     * ['price']
     * ['categoryId']
     * ['description']
     */
    public static function getServicesByServiceId($serviceID)
    {
        global $wpdb;
        $services = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}amelia_services WHERE id = %d", $serviceID);

        return $wpdb->get_results($services);
    }



    /**
     * Undocumented function
     *
     * @param int $Id
     * @return void
     */
    // function getSomeDataBy($Id)
    // {
    //     global $wpdb;
    //     $int_val = 1;
    //     $float_val = 3.14159;

    //     $prepared_sql = $wpdb->prepare(
    //         "SELECT * FROM {$wpdb->prefix}some_table WHERE some_column BETWEEN %d AND %f", [
    //             $int_val,
    //             $float_val,
    //         ]
    //     );
    // }







    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function orion_plugin_assets()
    {
        // Make column clickable.
        wp_register_script('make-column-clickable-elementor', ORION_PLUGIN_DIR_URL . 'assets/js/make-column-clickable.js', array('jquery'), ORION_VERSION, true);
        // Obfuscate.
        wp_enqueue_script('orion-ofuscate', ORION_PLUGIN_DIR_URL . 'assets/js/obfuscate.js', array('jquery'), ORION_VERSION, true);
        // Filters
        wp_enqueue_script('orion-filters', ORION_PLUGIN_DIR_URL . 'assets/js/filters.js', array('jquery'), ORION_VERSION, true);
        // Styles.
        // wp_enqueue_style('orion-styles', ORION_PLUGIN_DIR_URL . "assets/css/style.css", array(), ORION_VERSION);
    }
}
