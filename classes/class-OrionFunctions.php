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
        add_action('admin_menu', array($this, 'orion_add_tipocoach_taxonomy_admin_page'));
        add_action('show_user_profile', array($this, 'orion_edit_user_tipocoach_section'));
        add_action('edit_user_profile', array($this, 'orion_edit_user_tipocoach_section'));
        add_action('user_new_form', array($this, 'orion_edit_user_tipocoach_section'));
        add_action('personal_options_update', array($this, 'orion_save_user_department_terms'));
        add_action('edit_user_profile_update', array($this, 'orion_save_user_department_terms'));
        add_action('user_register', array($this, 'orion_save_user_department_terms'));
        // Filters
        add_filter('nav_menu_link_attributes', array($this, 'obfuscate_specific_menu_links'), 10, 3);
        add_filter('manage_edit-tipocoach_columns', array($this, 'orion_manage_tipocoach_user_column'));
        add_filter('manage_tipocoach_custom_column', array($this, 'orion_manage_tipocoach_column'), 10, 3);
        add_filter('sanitize_user', array($this, 'orion_disable_tipocoach_username'));
        add_filter('parent_file', array($this, 'orion_change_parent_file'));
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

    /**
     * Admin page for the 'tipocoach' taxonomy
     */
    function orion_add_tipocoach_taxonomy_admin_page()
    {
        $tax = get_taxonomy('tipocoach');
        add_users_page(
            esc_attr($tax->labels->menu_name),
            esc_attr($tax->labels->menu_name),
            $tax->cap->manage_terms,
            'edit-tags.php?taxonomy=' . $tax->name
        );
    }

    /**
     * Unsets the 'posts' column and adds a 'users' column on the manage tipocoach admin page.
     *
     * @param array $columns
     * @return void
     */
    function orion_manage_tipocoach_user_column($columns)
    {
        unset($columns['posts']);
        $columns['users'] = __('Users');
        return $columns;
    }

    /**
     * Handler columns
     * 
     * @param string $display WP just passes an empty string here.
     * @param string $column The name of the custom column.
     * @param int $term_id The ID of the term being displayed in the table.
     */
    function orion_manage_tipocoach_column($display, $column, $term_id)
    {
        if ('users' === $column) {
            $term = get_term($term_id, 'tipocoach');
            echo $term->count;
        }
    }

    /**
     * Section taxonomy edit
     * 
     * @param object $user The user object currently being edited.
     */
    function orion_edit_user_tipocoach_section($user)
    {
        global $pagenow;
        $tax = get_taxonomy('tipocoach');
        /* Make sure the user can assign terms of the tipocoach taxonomy before proceeding. */
        if (!current_user_can($tax->cap->assign_terms))
            return;
        /* Get the terms of the 'tipocoach' taxonomy. */
        $terms = get_terms('tipocoach', array('hide_empty' => false)); ?>
        <h3><?php _e('Tipo de coach'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="tipocoach"><?php _e('Allocated Tipo de coach'); ?></label></th>
                <td><?php
                    /* If there are any tipocoachs terms, loop through them and display checkboxes. */
                    if (!empty($terms)) {
                        echo orion_custom_form_field('tipocoach', $terms, $user->ID);
                    }
                    /* If there are no tipocoach terms, display a message. */ else {
                        _e('There are no tipocoach available.');
                    }
                    ?></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Return field as dropdown or checkbox, by default checkbox if no field type given
     * 
     * @param: name = taxonomy, options = terms avaliable, userId = user id to get linked terms
     */
    function orion_custom_form_field($name, $options, $userId, $type = 'checkbox')
    {
        switch ($type) {
            case 'checkbox':
                foreach ($options as $term) :
        ?>
                    <label for="tipocoach-<?php echo esc_attr($term->slug); ?>">
                        <input type="checkbox" name="tipocoach[]" id="tipocoach-<?php echo esc_attr($term->slug); ?>" value="<?php echo $term->slug; ?>" <?php if ($pagenow !== 'user-new.php') checked(true, is_object_in_term($userId, 'tipocoach', $term->slug)); ?>>
                        <?php echo $term->name; ?>
                    </label><br />
<?php
                endforeach;
                break;
            case 'dropdown':
                $selectTerms = [];
                foreach ($options as $term) {
                    $selectTerms[$term->term_id] = $term->name;
                }

                // get all terms linked with the user
                $usrTerms = get_the_terms($userId, 'tipocoach');
                $usrTermsArr = [];
                if (!empty($usrTerms)) {
                    foreach ($usrTerms as $term) {
                        $usrTermsArr[] = (int) $term->term_id;
                    }
                }
                // Dropdown
                echo "<select name='{$name}'>";
                echo "<option value=''>-Select-</option>";
                foreach ($selectTerms as $options_value => $options_label) {
                    $selected = (in_array($options_value, array_values($usrTermsArr))) ? " selected='selected'" : "";
                    echo "<option value='{$options_value}' {$selected}>{$options_label}</option>";
                }
                echo "</select>";
                break;
        }
    }

    /**
     * Save user data taxonomies
     * 
     * @param int $user_id The ID of the user to save the terms for.
     */
    function orion_save_user_department_terms($user_id)
    {
        $tax = get_taxonomy('tipocoach');
        /* Make sure the current user can edit the user and assign terms before proceeding. */
        if (!current_user_can('edit_user', $user_id) && current_user_can($tax->cap->assign_terms))
            return false;
        $term = $_POST['tipocoach'];
        $terms = is_array($term) ? $term : (int) $term; // fix for checkbox and select input field
        /* Sets the terms (we're just using a single term) for the user. */
        wp_set_object_terms($user_id, $terms, 'tipocoach', false);
        clean_object_term_cache($user_id, 'tipocoach');
    }

    /**
     * Restrict Username Same as Taxonomy
     * 
     * @param string $username The username of the user before registration is complete.
     */
    function orion_disable_tipocoach_username($username)
    {
        if ('tipocoach' === $username)
            $username = '';
        return $username;
    }

    /**
     * Highlight Active Menu Item
     * 
     * Update parent file name to fix the selected menu issue
     */
    function cb_change_parent_file($parent_file)
    {
        global $submenu_file;
        if (
            isset($_GET['taxonomy']) &&
            $_GET['taxonomy'] == 'tipocoach' &&
            $submenu_file == 'edit-tags.php?taxonomy=tipocoach'
        )
            $parent_file = 'users.php';
        return $parent_file;
    }

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
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
