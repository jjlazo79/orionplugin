<?php

declare(strict_types=1);
/**
 * Class OrionUserMeta
 *
 * Add new metas and fields to users
 * @package WordPress
 * @subpackage Orionplugin
 * @since Orionplugin 1.1.2
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) die('Bad dog. No biscuit!');

/**
 * The main OrionUserMeta class
 */
class OrionUserMeta
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
            self::$instance = new OrionUserMeta();
        }

        return self::$instance;
    }


    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct()
    {
        // Actions
        add_action('admin_menu', array($this, 'orion_add_tipocoach_taxonomy_admin_page'));
        add_action('show_user_profile', array($this, 'orion_edit_user_tipocoach_section'));
        add_action('edit_user_profile', array($this, 'orion_edit_user_tipocoach_section'));
        add_action('user_new_form', array($this, 'orion_edit_user_tipocoach_section'));
        add_action('personal_options_update', array($this, 'orion_save_user_tipocoach_terms'));
        add_action('edit_user_profile_update', array($this, 'orion_save_user_tipocoach_terms'));
        add_action('user_register', array($this, 'orion_save_user_tipocoach_terms'));
        add_action('register_form', array($this, 'orion_registration_form'));
        // Filters
        add_filter('manage_edit-tipocoach_columns', array($this, 'orion_manage_tipocoach_user_column'));
        add_filter('manage_users_columns', array($this, 'orion_manage_users_column'));
        add_filter('manage_tipocoach_custom_column', array($this, 'orion_manage_tipocoach_column'), 10, 3);
        add_filter('manage_users_custom_column', array($this, 'orion_manage_user_column'), 10, 3);
        add_filter('sanitize_user', array($this, 'orion_disable_tipocoach_username'));
        add_filter('parent_file', array($this, 'orion_change_parent_file'));
        add_filter('pre_user_query', array($this, 'wp_user_query_random_enable'), 1);
        add_filter('template_include', array($this, 'orion_author_template_loader'), 1);
        add_filter('registration_errors', array($this, 'orion_registration_errors'), 10, 3);
    }

    /**
     * Admin page for the 'tipocoach' taxonomy
     */
    public function orion_add_tipocoach_taxonomy_admin_page()
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
    public function orion_manage_tipocoach_user_column($columns)
    {
        unset($columns['posts']);
        $columns['users'] = __('Coaches', ORION_TEXT_DOMAIN);
        return $columns;
    }

    /**
     * Add custom user column
     *
     * @param array $columns
     * @return void
     */
    public function orion_manage_users_column($columns)
    {
        $columns['tipocoach'] = __('Tipo Coach', ORION_TEXT_DOMAIN);
        return $columns;
    }

    /**
     * Handler custom taxonomy columns
     * 
     * @param string $display WP just passes an empty string here.
     * @param string $column The name of the custom column.
     * @param int $term_id The ID of the term being displayed in the table.
     */
    public function orion_manage_tipocoach_column($display, $column, $term_id)
    {
        if ('users' === $column) {
            $term = get_term($term_id, 'tipocoach');
            echo $term->count;
        }
    }

    /**
     * Adds Content To The Custom Added Column
     *
     * @param [type] $value
     * @param [type] $column_name
     * @param [type] $user_id
     * @return void
     */
    public function orion_manage_user_column($value, $column_name, $user_id)
    {
        $user = get_userdata($user_id);
        if ('tipocoach' == $column_name) {
            $user_terms = wp_get_object_terms($user_id, 'tipocoach');

            if (!empty($user_terms)) {
                if (!is_wp_error($user_terms)) {
                    $value .= '<ul>';
                    foreach ($user_terms as $term) {
                        $value .= '<li><a href="' . esc_url(get_term_link($term->slug, 'tipocoach')) . '">' . esc_html($term->name) . '</a></li>';
                    }
                    $value .= '</ul>';
                }
            }
        }
        return $value;
    }

    /**
     * Section taxonomy edit
     * 
     * @param object $user The user object currently being edited.
     */
    public function orion_edit_user_tipocoach_section($user)
    {
        $tax = get_taxonomy('tipocoach');
        /* Make sure the user can assign terms of the tipocoach taxonomy before proceeding. */
        if (!current_user_can($tax->cap->assign_terms))
            return;
        /* Get the terms of the 'tipocoach' taxonomy. */
        $terms              = get_terms('tipocoach', array('hide_empty' => false));
        $long_description   = get_user_meta($user->ID, 'long_description', true);
        $user_certification = get_user_meta($user->ID, 'icf_certification', true);
        $provider_phone     = get_user_meta($user->ID, 'provider_phone', true);
?>
        <h3><?php _e('Tipo de coach'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="tipocoach"><?php _e('Asignar tipo de coach'); ?></label></th>
                <td><?php
                    /* If there are any tipocoachs terms, loop through them and display checkboxes. */
                    if (!empty($terms)) {
                        echo OrionUserMeta::orion_custom_form_field('tipocoach', $terms, $user->ID);
                    }
                    /* If there are no tipocoach terms, display a message. */ else {
                        _e('No hay tipocoach disponibles.');
                    }
                    ?></td>
            </tr>
            <tr>
                <th><label for="icf_certification"><?php _e('Certificación ICF'); ?></label></th>
                <td>
                    <?php
                    $icf_certifications = array("ACC", "PCC", "MCC");
                    echo '<select name="icf_certification">';
                    foreach ($icf_certifications as $icf_certification) {
                        echo "<option value='{$icf_certification}' " . selected($user_certification, $icf_certification) . ">{$icf_certification}</option>";
                    }
                    echo '</select>';
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="long_description"><?php _e('Descripción larga'); ?></label></th>
                <td>
                    <?php
                    wp_editor(htmlspecialchars_decode($long_description), 'long_description', array("media_buttons" => false));
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="provider_phone"><?php esc_html_e('Phone number', ORION_TEXT_DOMAIN); ?></label> <span class="description"><?php esc_html_e('(required)', ORION_TEXT_DOMAIN); ?></span></th>
                <td>
                    <input type="tel" pattern="[0-9]{3} [0-9]{2} [0-9]{2} [0-9]{2}" placeholder="Ej. 666 22 33 44" id="provider_phone" name="provider_phone" value="<?php echo esc_attr($provider_phone); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Return field as dropdown or checkbox, by default checkbox if no field type given
     * 
     * @link https://codebriefly.com/how-to-create-taxonomy-for-users-in-wordpress/#Create_Admin_Page
     * 
     * @param string $name The name of taxonomy
     * @param array $options The terms avaliable
     * @param int $userId The user id to get linked terms
     * @param string $type The tipe of elements
     */
    public static function orion_custom_form_field($name, $options, $userId, $type = 'checkbox')
    {
        global $pagenow;
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
                    $selectTerms[$term->term_slug] = $term->name;
                }

                // Get all terms linked with the user
                $usrTerms = get_the_terms($userId, 'tipocoach'); // ¿wp_get_object_terms?
                $usrTermsArr = [];
                if (!empty($usrTerms)) {
                    foreach ($usrTerms as $term) {
                        $usrTermsArr[] = $term->term_slug;
                    }
                }
                // Dropdown
                var_dump($usrTerms);
                echo "<select name='{$name}' multiple>";
                echo "<option value=''>-Select-</option>";
                foreach ($options as $term) {
                    $selected = (in_array($term->slug, array_values($usrTermsArr))) ? " selected='selected'" : "";
                    echo "<option value='{$term->slug}' {$selected}>{$term->name}</option>";
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
    public function orion_save_user_tipocoach_terms($user_id)
    {
        global $wpdb;
        $tax = get_taxonomy('tipocoach');
        /* Make sure the current user can edit the user and assign terms before proceeding. */
        if (!current_user_can('edit_user', $user_id) && current_user_can($tax->cap->assign_terms))
            return false;

        if (!empty($_POST['tipocoach'])) {
            $terms = $_POST['tipocoach'];
            // $terms = is_array($term) ? $term : $term; // fix for checkbox and select input field
            /* Sets the terms (we're just using a single term) for the user. */
            wp_set_object_terms($user_id, $terms, 'tipocoach', false);
        }

        if (!empty($_POST['long_description'])) {
            $data = htmlspecialchars($_POST['long_description']);
            update_user_meta($user_id, 'long_description', $data);
        }
        if (!empty($_POST['icf_certification'])) {
            $data = sanitize_text_field($_POST['icf_certification']);
            update_user_meta($user_id, 'icf_certification', $data);
        }
        if (!empty($_POST['provider_phone'])) {
            update_user_meta($user_id, 'provider_phone', intval($_POST['provider_phone']));
        }

        /* Save into amelia_user table too */
        // $terms_update = json_encode($terms); // maybe_serialize($terms);
        // $wpdb->update($wpdb->prefix . 'amelia_users', array('coach_category_slug' => $terms_update), array('externalId' => $user_id));
        clean_object_term_cache($user_id, 'tipocoach');
    }

    /**
     * Restrict Username Same as Taxonomy
     * 
     * @param string $username The username of the user before registration is complete.
     */
    public function orion_disable_tipocoach_username($username)
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
    public function orion_change_parent_file($parent_file)
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
     * Enable random user query
     *
     * @param string $query
     * @return string
     */
    public function wp_user_query_random_enable($query)
    {
        if ($query->query_vars["orderby"] == 'rand') {
            $query->query_orderby = 'ORDER by RAND()';
        }
    }

    /**
     * Load custom author template
     *
     * @param string $template
     * @return string
     */
    public function orion_author_template_loader($template)
    {

        $file = '';

        if (is_author()) {
            $file   = 'orion-author.php'; // the name of your custom template
            $find[] = $file;
            $find[] = ORION_TEXT_DOMAIN . '/' . $file; // name of folder it could be in, in user's theme
        }

        if ($file) {
            $template       = locate_template(array_unique($find));
            if (!$template) {
                // if not found in theme, will use your plugin version
                $template = untrailingslashit(ORION_PLUGIN_DIR_PATH) . '/templates/' . $file;
            }
        }

        return $template;
    }

    /**
     * Add custom fields to register
     *
     * @return void
     */
    public function orion_registration_form()
    {

        $year = !empty($_POST['provider_phone']) ? intval($_POST['provider_phone']) : '';

        ?>
        <p>
            <label for="provider_phone"><?php esc_html_e('Phone number', ORION_TEXT_DOMAIN) ?><br />
                <input type="tel" pattern="[0-9]{3} [0-9]{2} [0-9]{2} [0-9]{2}" id="provider_phone" name="provider_phone" value="<?php echo esc_attr($year); ?>" class="input" />
            </label>
        </p>
<?php
    }

    public function orion_registration_errors($errors, $sanitized_user_login, $user_email)
    {

        if (empty($_POST['provider_phone'])) {
            $errors->add('provider_phone_error', __('<strong>ERROR</strong>: Please enter your Phone number.', ORION_TEXT_DOMAIN));
        }

        if (!empty($_POST['provider_phone']) && intval($_POST['provider_phone']) < 1900) {
            $errors->add('provider_phone_error', __('<strong>ERROR</strong>: You must be born after 1900.', ORION_TEXT_DOMAIN));
        }

        return $errors;
    }
}
