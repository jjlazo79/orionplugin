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
            'slider'   => 'false',
            'type'     => '1',
            'category' => 'all'
        );

        $a = shortcode_atts($default, $atts);
        $and = '';
        if ('all' !== $a['category']) {
            $and = " AND `coach_category_slug` LIKE '%" . $a['category'] . "%'";
        }

        $coaches = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' $and");
        // exit(var_dump($wpdb->last_query));
        ob_start();
        echo 'Slider: ';
        var_dump($a['slider']);
        echo '<br><br>Type: ';
        var_dump($a['type']);
        echo '<br><br>Category: ';
        var_dump($a['category']);
        if ('false' !== $a['slider']) {
            # Carousel type 1 and 2
            if ('2' !== $a['type']) {
?>
                <div id="wrapper">
                    <div id="carousel" class="carousel-type-1">
                        <div id="content">
                            <?php foreach ($coaches as $coach) : ?>
                                <div class="item">
                                    <div class="item-img" style="background-image: url('<?php echo $coach->pictureFullPath; ?>');">
                                        <span></span>
                                    </div>
                                    <div class="item-content">
                                        <p><?php echo $coach->firstName . ' ' . $coach->lastName; ?></p>
                                        <p><?php echo $coach->description; ?></p>
                                        <a target="_blank" href="<?php echo get_author_posts_url($coach->externalId); ?>"><?php _e('See profile', ORION_TEXT_DOMAIN); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button id="prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0V0z" />
                            <path d="M15.61 7.41L14.2 6l-6 6 6 6 1.41-1.41L11.03 12l4.58-4.59z" />
                        </svg>
                    </button>
                    <button id="next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0V0z" />
                            <path d="M10.02 6L8.61 7.41 13.19 12l-4.58 4.59L10.02 18l6-6-6-6z" />
                        </svg>
                    </button>
                </div>

            <?php
            } else {
            ?>
                <div id="wrapper">
                    <div id="carousel" class="carousel-type-2">
                        <div id="content">
                            <?php foreach ($coaches as $coach) : ?>
                                <div class="item">
                                    <div class="item-img" style="background-image: url('<?php echo $coach->pictureFullPath; ?>');">
                                        <span></span>
                                    </div>
                                    <div class="item-content">
                                        <p><?php echo $coach->firstName . ' ' . $coach->lastName; ?></p>
                                        <p><?php echo $coach->description; ?></p>
                                        <a target="_blank" href="<?php echo get_author_posts_url($coach->externalId); ?>"><?php _e('See profile', ORION_TEXT_DOMAIN); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button id="prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0V0z" />
                            <path d="M15.61 7.41L14.2 6l-6 6 6 6 1.41-1.41L11.03 12l4.58-4.59z" />
                        </svg>
                    </button>
                    <button id="next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0V0z" />
                            <path d="M10.02 6L8.61 7.41 13.19 12l-4.58 4.59L10.02 18l6-6-6-6z" />
                        </svg>
                    </button>
                </div>
            <?php
            }
        } else {
            ?> <!-- List flex -->
            <div id="wrapper">
                <div id="flex-list">
                    <div id="content">
                        <?php foreach ($coaches as $coach) : ?>
                            <div class="item">
                                <div class="item-img" style="background-image: url('<?php echo $coach->pictureFullPath; ?>');">
                                    <span></span>
                                </div>
                                <div class="item-content">
                                    <p><?php echo $coach->firstName . ' ' . $coach->lastName; ?></p>
                                    <p><?php echo $coach->description; ?></p>
                                    <a target="_blank" href="<?php echo get_author_posts_url($coach->externalId); ?>"><?php _e('See profile', ORION_TEXT_DOMAIN); ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
<?php
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
