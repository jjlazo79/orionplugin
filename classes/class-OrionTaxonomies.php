<?php

declare(strict_types=1);
/**
 * Class OrionTaxonomies
 *
 * Handles the creation of custom taxonomies
 * @package WordPress
 * @subpackage Orion
 * @since OrionPlugin 1.1.0
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) die('Bad dog. No biscuit!');

class OrionTaxonomies
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
            self::$instance = new OrionTaxonomies();
        }

        return self::$instance;
    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
        add_action('init', array($this, 'registerTaxonomies'));
    }

    /**
     * Register custom taxonomies
     *
     * @return void
     */
    public function registerTaxonomies()
    {
        // Add new taxonomy, hierarchical (like categories)
        $labels = [
            'name'                       => __('Tipo de coaches', ORION_TEXT_DOMAIN),
            'singular_name'              => _x('Tipo de coach', 'taxonomy general name', ORION_TEXT_DOMAIN),
            'search_items'               => __('Search Tipo de coaches', ORION_TEXT_DOMAIN),
            'popular_items'              => __('Popular Tipo de coaches', ORION_TEXT_DOMAIN),
            'all_items'                  => __('All Tipo de coaches', ORION_TEXT_DOMAIN),
            'parent_item'                => __('Parent Tipo de coach', ORION_TEXT_DOMAIN),
            'parent_item_colon'          => __('Parent Tipo de coach:', ORION_TEXT_DOMAIN),
            'edit_item'                  => __('Edit Tipo de coach', ORION_TEXT_DOMAIN),
            'update_item'                => __('Update Tipo de coach', ORION_TEXT_DOMAIN),
            'view_item'                  => __('View Tipo de coach', ORION_TEXT_DOMAIN),
            'add_new_item'               => __('Add New Tipo de coach', ORION_TEXT_DOMAIN),
            'new_item_name'              => __('New Tipo de coach', ORION_TEXT_DOMAIN),
            'separate_items_with_commas' => __('Separate Tipo de coaches with commas', ORION_TEXT_DOMAIN),
            'add_or_remove_items'        => __('Add or remove Tipo de coaches', ORION_TEXT_DOMAIN),
            'choose_from_most_used'      => __('Choose from the most used Tipo de coaches', ORION_TEXT_DOMAIN),
            'not_found'                  => __('No Tipo de coaches found.', ORION_TEXT_DOMAIN),
            'no_terms'                   => __('No Tipo de coaches', ORION_TEXT_DOMAIN),
            'menu_name'                  => __('Tipo de coaches', ORION_TEXT_DOMAIN),
            'items_list_navigation'      => __('Tipo de coaches list navigation', ORION_TEXT_DOMAIN),
            'items_list'                 => __('Tipo de coaches list', ORION_TEXT_DOMAIN),
            'most_used'                  => _x('Most Used', 'tipocoach', ORION_TEXT_DOMAIN),
            'back_to_items'              => __('&larr; Back to Tipo de coaches', ORION_TEXT_DOMAIN),
        ];

        $args = [
            'labels'                => $labels,
            'hierarchical'          => true,
            'public'                => true,
            'show_in_nav_menus'     => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'show_tagcloud'         => true,
        ];

        register_taxonomy('tipocoach', ['user'], $args);

        unset($labels);
        unset($args);
    }
}
