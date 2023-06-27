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
     * Taxonomy slug
     */
    public $taxonomy = 'tipocoach';

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
            'employees_filter_list',
            array($this, 'orion_employees_filter_list_shortcode_handler')
        );
        add_shortcode(
            'employees_slider_list',
            array($this, 'orion_employees_slider_list_shortcode_handler')
        );
        add_shortcode(
            'employees_flex_list',
            array($this, 'orion_employees_flex_list_shortcode_handler')
        );
    }

    /**
     * Register shortcode employees_filter_list
     */
    public function orion_employees_filter_list_shortcode_handler()
    {
        global $wpdb;

        // Only in shortcode page insert
        OrionShortcodes::orion_enqueue_front_scripts();

        $tipocoach = (isset($_GET["tipocoaching"])) ? '&tipocoaching=' . $_GET["tipocoaching"] : '';
        $objetivo  = (isset($_GET["objetivo"])) ? $_GET["objetivo"] : '';
        $precio    = (isset($_GET["precio"])) ? '&precio=' . $_GET["precio"] : '';

        if ("false" == $objetivo || null === $objetivo) {
            $args = array(
                'role'    => 'wpamelia-provider',
                'orderby' => 'rand'
            );
            $coaches   = get_users($args);
        } else {
            $term  = get_term_by('slug', $objetivo, $this->taxonomy);
            $users = get_objects_in_term($term->term_id, $this->taxonomy);
            $args  = array(
                'role'    => 'wpamelia-provider',
                'orderby' => 'rand',
                'include' => $users
            );
            $user_query = new WP_User_Query($args);
            if (!empty($user_query->results))
                $coaches = $user_query->results;
            $objetivo = '&objetivo=' . $objetivo;
        }

        ob_start();

?>
        <div id="wrapper">
            <div id="filter-list">
                <div class="content">
                    <?php foreach ($coaches as $coach) : ?>
                        <?php $wpamelia_provider = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' AND externalId = $coach->ID", OBJECT);
                        if (empty($wpamelia_provider)) continue; ?>
                        <div class="item">
                            <div class="item-img" style="background-image: url('<?php echo $wpamelia_provider[0]->pictureThumbPath; ?>');">
                                <span></span>
                            </div>
                            <div class="item-content">
                                <p><?php echo $wpamelia_provider[0]->firstName . ' ' . $wpamelia_provider[0]->lastName; ?></p>
                                <p><?php echo $wpamelia_provider[0]->description; ?></p>
                                <div class="item-content--buttons">
                                    <a class="button-see" target="_blank" href="<?php echo get_author_posts_url($coach->ID); ?>?coach=<?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?><?php echo $tipocoach; ?><?php echo $precio; ?>">
                                        <?php _e('See profile', ORION_TEXT_DOMAIN); ?>
                                    </a>
                                    <a class="button-reserve" target="_blank" href="/pedir-cita-coach/?coach=<?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?><?php echo $tipocoach; ?><?php echo $precio; ?>">
                                        <?php _e('Reserve', ORION_TEXT_DOMAIN); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php

        $output = ob_get_clean();

        return $output;
    }

    /**
     * Register shortcode employees_slider_list
     */
    public function orion_employees_slider_list_shortcode_handler($atts)
    {
        global $wpdb;

        // Only in shortcode page insert
        OrionShortcodes::orion_enqueue_front_scripts();

        $buttons = '
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
            </button>';



        $default = array(
            'type'          => '1',
            'category_slug' => 'all'
        );

        $a   = shortcode_atts($default, $atts);

        $objetivo = ('all' !== $a['category_slug']) ? $a['category_slug'] : null;

        if ('all' !== $objetivo || null === $objetivo) {
            $args = array(
                'role'    => 'wpamelia-provider',
                'orderby' => 'rand'
            );
            $coaches   = get_users($args);
        } else {
            $term  = get_term_by('slug', $objetivo, $this->taxonomy);
            $users = get_objects_in_term($term->term_id, $this->taxonomy);
            $args  = array(
                'role'    => 'wpamelia-provider',
                'orderby' => 'rand',
                'include' => $users
            );
            $user_query = new WP_User_Query($args);
            if (!empty($user_query->results)) $coaches = $user_query->results;
        }

        ob_start();

        // Carousel type 1 and 2.
        if ('2' !== $a['type']) { ?>
            <div id="wrapper">
                <div id="carousel-type-1" class="carousel">
                    <div class="content">
                        <?php foreach ($coaches as $coach) : ?>
                            <?php $wpamelia_provider = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' AND externalId = $coach->ID", OBJECT);
                            if (empty($wpamelia_provider)) continue;
                            if ('visible' !== $wpamelia_provider[0]->status) continue;
                            $terms = get_the_terms($coach->ID, $this->taxonomy);
                            $wpamelia_provider[0]->tipocoaching = (!empty($terms)) ? $terms[0]->slug : '';
                            // echo '<pre>';
                            // var_dump($coach);
                            // echo '</pre>';
                            // echo '<pre>';

                            // var_dump($wpamelia_provider);
                            // echo '</pre>'; 
                            ?>
                            <div class="item">
                                <div class="item-img" style="background-image: url('<?php echo $wpamelia_provider[0]->pictureFullPath; ?>');">
                                    <span></span>
                                </div>
                                <div class="item-content">
                                    <p><?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?></p>
                                    <p><?php echo $wpamelia_provider[0]->description; ?></p>
                                    <a class="button-see" target="_blank" href="<?php echo get_author_posts_url($coach->ID); ?>"><?php _e('See profile', ORION_TEXT_DOMAIN); ?></a>
                                    <a class="button-reserve" target="_blank" href="/pedir-cita-coach/?coach=<?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?><?php echo $tipocoach; ?><?php echo $precio; ?>"><?php _e('Reserve', ORION_TEXT_DOMAIN); ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php echo $buttons; ?>
            </div>

        <?php
        } else {
        ?>
            <div id="wrapper">
                <div id="carousel-type-2" class="carousel">
                    <div class="content">
                        <?php foreach ($coaches as $coach) : ?>
                            <?php $wpamelia_provider = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' AND externalId = $coach->ID", OBJECT);
                            if (empty($wpamelia_provider)) continue; ?>
                            <div class="item">
                                <div class="item-img" style="background-image: url('<?php echo $wpamelia_provider[0]->pictureThumbPath; ?>');">
                                    <span></span>
                                </div>
                                <div class="item-content">
                                    <p><?php echo $wpamelia_provider[0]->firstName . ' ' . $wpamelia_provider[0]->lastName; ?></p>
                                    <p><?php echo $wpamelia_provider[0]->description; ?></p>
                                    <a class="button-see" target="_blank" href="<?php echo get_author_posts_url($coach->ID); ?>"><?php _e('See profile', ORION_TEXT_DOMAIN); ?></a>
                                    <a class="button-reserve" target="_blank" href="/pedir-cita-coach/?coach=<?php echo $wpamelia_provider[0]->firstName  . ' ' . $wpamelia_provider[0]->lastName; ?><?php echo $tipocoach; ?><?php echo $precio; ?>"><?php _e('Reserve', ORION_TEXT_DOMAIN); ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php echo $buttons; ?>
            </div>
        <?php
        }

        $output = ob_get_clean();

        return $output;
    }



    /**
     * Register shortcode employees_flex_list
     */
    public function orion_employees_flex_list_shortcode_handler($atts)
    {
        global $wpdb;

        // Only in shortcode page insert
        OrionShortcodes::orion_enqueue_front_scripts();

        $default = array(
            'category_slug' => 'all'
        );

        $a   = shortcode_atts($default, $atts);

        $objetivo = ('all' !== $a['category_slug']) ? $a['category_slug'] : null;

        if ('all' !== $objetivo || null === $objetivo) {
            $args = array(
                'role'    => 'wpamelia-provider',
                'orderby' => 'rand'
            );
            $coaches   = get_users($args);
        } else {
            $term  = get_term_by('slug', $objetivo, $this->taxonomy);
            $users = get_objects_in_term($term->term_id, $this->taxonomy);
            $args  = array(
                'role'    => 'wpamelia-provider',
                'orderby' => 'rand',
                'include' => $users
            );
            $user_query = new WP_User_Query($args);
            if (!empty($user_query->results)) $coaches = $user_query->results;
        }

        ob_start();

        // List flex. 
        ?>
        <div id="wrapper">
            <div id="flex-list">
                <div class="content">
                    <?php foreach ($coaches as $coach) : ?>
                        <?php $wpamelia_provider = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amelia_users WHERE type = 'provider' AND externalId = $coach->ID", OBJECT);
                        if (empty($wpamelia_provider)) continue; ?>
                        <div class="item">
                            <div class="item-img">
                                <img src="<?php echo $wpamelia_provider[0]->pictureThumbPath; ?>" alt="<?php echo $wpamelia_provider[0]->firstName; ?>">
                            </div>
                            <div class="item-content">
                                <p><?php echo $wpamelia_provider[0]->firstName . ' ' . $wpamelia_provider[0]->lastName; ?></p>
                                <p><?php echo $wpamelia_provider[0]->description; ?></p>
                                <a class="button" target="_blank" href="<?php echo get_author_posts_url($coach->ID); ?>"><?php _e('See profile', ORION_TEXT_DOMAIN); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php

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
