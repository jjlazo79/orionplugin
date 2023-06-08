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
            'aplications_data',
            array($this, 'orion_aplications_shortcode_handler')
        );
    }

    /**
     * Register shortcode artículo destacado
     */
    public function orion_aplications_shortcode_handler($atts)
    {
        // Only in shortcode page insert
        // OrionShortcodes::orion_enqueue_front_scripts();

        $orion_aplications_options = get_post_meta(get_the_ID(), '_orion_aplications_options', true) ? get_post_meta(get_the_ID(), '_orion_aplications_options', true) : array();

        ob_start();

        if (!empty($orion_aplications_options)) {
            echo '<h3 style="
                    color: var(--e-global-color-primary );
                    font-family: var(--e-global-typography-primary-font-family ), Sans-serif;
                    font-weight: var(--e-global-typography-primary-font-weight );
                    margin-bottom: 30px;
                ">' . __('Aplications', ORION_TEXT_DOMAIN) . '</h3>';
            echo '<div class="aplicacion row">';
            foreach ($orion_aplications_options as $aplication) {
                $first_word = strtok($aplication, " ");
                $img_uri    = sanitize_title($first_word);
                echo '<div class="media fondo-gris-claro-2 p-2 ">
                    <img class="mr-2 align-self-center" src="https://orion.es/wp-content/uploads/2018/01/' . $img_uri . '.png" alt="' . $aplication . '">
                    <div class="media-body align-self-center">' . $aplication . '</div>
                </div>';
            }
            echo '</div>';
        }

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
