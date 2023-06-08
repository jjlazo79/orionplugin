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
        add_action('elementor/element/column/layout/before_section_end', array($this, 'widget_extensions'), 10, 2);
        add_action('elementor/frontend/column/before_render', array($this, 'before_render_options'), 10);
        add_action('wp_loaded', array($this, 'buffer_start'));
        add_action('wp_enqueue_scripts', array($this, 'orion_plugin_assets'));
        // Filters
        add_filter('nav_menu_link_attributes', array($this, 'obfuscate_specific_menu_links'), 10, 3);
    }


    /**
     * After layout callback
     *
     * @param  object $element
     * @param  array $args
     * @return void
     */
    public function widget_extensions($element, $args)
    {
        $element->add_control(
            'column_link',
            [
                'label'       => __('Column Link', ORION_TEXT_DOMAIN),
                'type'        => Elementor\Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com', 'elementor'),
                'selectors'   => [],
            ]
        );
    }


    public function before_render_options($element)
    {
        $settings  = $element->get_settings_for_display();


        if (isset($settings['column_link'], $settings['column_link']['url']) && !empty($settings['column_link']['url'])) {
            wp_enqueue_script('make-column-clickable-elementor');


            // start of WPML
            do_action('wpml_register_single_string', 'Make Column Clickable Elementor', 'Link - ' . $settings['column_link']['url'], $settings['column_link']['url']);
            $settings['column_link']['url'] = apply_filters('wpml_translate_single_string', $settings['column_link']['url'], 'Make Column Clickable Elementor', 'Link - ' . $settings['column_link']['url']);
            // end of WPML


            $element->add_render_attribute('_wrapper', 'class', 'make-column-clickable-elementor');
            $element->add_render_attribute('_wrapper', 'style', 'cursor: pointer;');
            $element->add_render_attribute('_wrapper', 'data-column-clickable', $settings['column_link']['url']);
            $element->add_render_attribute('_wrapper', 'data-column-clickable-blank', $settings['column_link']['is_external'] ? '_blank' : '_self');
        }
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

    public function orion_plugin_assets()
    {
        // Make column clickable
        wp_register_script('make-column-clickable-elementor', ORION_PLUGIN_DIR_URL . '/assets/js/make-column-clickable.js', array('jquery'), ORION_VERSION, true);
        // Obfuscate
        wp_enqueue_script('orion-ofuscate', ORION_PLUGIN_DIR_URL . '/assets/js/obfuscate.js', array('jquery'), ORION_VERSION, true);
        // Styles
        wp_enqueue_style('orion-styles', ORION_PLUGIN_DIR_URL . "/assets/css/style.css", array(), ORION_VERSION);
    }
}
