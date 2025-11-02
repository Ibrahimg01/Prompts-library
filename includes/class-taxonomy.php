<?php
/**
 * Taxonomy Handler
 *
 * @package Prompts_Library
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Taxonomy Class
 */
class Prompts_Library_Taxonomy {

    /**
     * Single instance
     *
     * @var Prompts_Library_Taxonomy
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library_Taxonomy
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'prompt_category_add_form_fields', array( $this, 'add_category_color_field' ) );
        add_action( 'prompt_category_edit_form_fields', array( $this, 'edit_category_color_field' ) );
        add_action( 'created_prompt_category', array( $this, 'save_category_color' ) );
        add_action( 'edited_prompt_category', array( $this, 'save_category_color' ) );
    }

    /**
     * Get taxonomy capability mapping.
     *
     * @return array
     */
    public static function get_capabilities() {
        return array(
            'manage_terms' => 'manage_prompt_terms',
            'edit_terms'   => 'edit_prompt_terms',
            'delete_terms' => 'delete_prompt_terms',
            'assign_terms' => 'assign_prompt_terms',
        );
    }

    /**
     * Register taxonomies
     */
    public static function register_taxonomies() {
        $is_main_site = is_main_site();

        // Register Category Taxonomy
        $category_labels = array(
            'name'                       => _x( 'Categories', 'Taxonomy General Name', 'prompts-library' ),
            'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'prompts-library' ),
            'menu_name'                  => __( 'Categories', 'prompts-library' ),
            'all_items'                  => __( 'All Categories', 'prompts-library' ),
            'parent_item'                => __( 'Parent Category', 'prompts-library' ),
            'parent_item_colon'          => __( 'Parent Category:', 'prompts-library' ),
            'new_item_name'              => __( 'New Category Name', 'prompts-library' ),
            'add_new_item'               => __( 'Add New Category', 'prompts-library' ),
            'edit_item'                  => __( 'Edit Category', 'prompts-library' ),
            'update_item'                => __( 'Update Category', 'prompts-library' ),
            'view_item'                  => __( 'View Category', 'prompts-library' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'prompts-library' ),
            'add_or_remove_items'        => __( 'Add or remove categories', 'prompts-library' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'prompts-library' ),
            'popular_items'              => __( 'Popular Categories', 'prompts-library' ),
            'search_items'               => __( 'Search Categories', 'prompts-library' ),
            'not_found'                  => __( 'Not Found', 'prompts-library' ),
            'no_terms'                   => __( 'No categories', 'prompts-library' ),
            'items_list'                 => __( 'Categories list', 'prompts-library' ),
            'items_list_navigation'      => __( 'Categories list navigation', 'prompts-library' ),
        );

        $category_args = array(
            'labels'                     => $category_labels,
            'hierarchical'               => true,
            'public'                     => false,
            'show_ui'                    => $is_main_site,
            'show_admin_column'          => $is_main_site,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => false,
            'capabilities'               => self::get_capabilities(),
        );

        register_taxonomy( 'prompt_category', array( 'prompt' ), $category_args );

        // Register Tag Taxonomy
        $tag_labels = array(
            'name'                       => _x( 'Tags', 'Taxonomy General Name', 'prompts-library' ),
            'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'prompts-library' ),
            'menu_name'                  => __( 'Tags', 'prompts-library' ),
            'all_items'                  => __( 'All Tags', 'prompts-library' ),
            'parent_item'                => __( 'Parent Tag', 'prompts-library' ),
            'parent_item_colon'          => __( 'Parent Tag:', 'prompts-library' ),
            'new_item_name'              => __( 'New Tag Name', 'prompts-library' ),
            'add_new_item'               => __( 'Add New Tag', 'prompts-library' ),
            'edit_item'                  => __( 'Edit Tag', 'prompts-library' ),
            'update_item'                => __( 'Update Tag', 'prompts-library' ),
            'view_item'                  => __( 'View Tag', 'prompts-library' ),
            'separate_items_with_commas' => __( 'Separate tags with commas', 'prompts-library' ),
            'add_or_remove_items'        => __( 'Add or remove tags', 'prompts-library' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'prompts-library' ),
            'popular_items'              => __( 'Popular Tags', 'prompts-library' ),
            'search_items'               => __( 'Search Tags', 'prompts-library' ),
            'not_found'                  => __( 'Not Found', 'prompts-library' ),
            'no_terms'                   => __( 'No tags', 'prompts-library' ),
            'items_list'                 => __( 'Tags list', 'prompts-library' ),
            'items_list_navigation'      => __( 'Tags list navigation', 'prompts-library' ),
        );

        $tag_args = array(
            'labels'                     => $tag_labels,
            'hierarchical'               => false,
            'public'                     => false,
            'show_ui'                    => $is_main_site,
            'show_admin_column'          => $is_main_site,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => false,
            'capabilities'               => self::get_capabilities(),
        );

        register_taxonomy( 'prompt_tag', array( 'prompt' ), $tag_args );
    }

    /**
     * Add color field to category add form
     */
    public function add_category_color_field() {
        ?>
        <div class="form-field">
            <label for="category_color"><?php esc_html_e( 'Badge Color', 'prompts-library' ); ?></label>
            <input type="color" name="category_color" id="category_color" value="#8b5cf6" />
            <p class="description"><?php esc_html_e( 'Choose a color for the category badge', 'prompts-library' ); ?></p>
        </div>
        <?php
    }

    /**
     * Add color field to category edit form
     */
    public function edit_category_color_field( $term ) {
        $color = get_term_meta( $term->term_id, 'category_color', true );
        if ( empty( $color ) ) {
            $color = '#8b5cf6';
        }
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="category_color"><?php esc_html_e( 'Badge Color', 'prompts-library' ); ?></label>
            </th>
            <td>
                <input type="color" name="category_color" id="category_color" value="<?php echo esc_attr( $color ); ?>" />
                <p class="description"><?php esc_html_e( 'Choose a color for the category badge', 'prompts-library' ); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save category color
     */
    public function save_category_color( $term_id ) {
        if ( isset( $_POST['category_color'] ) ) {
            update_term_meta( $term_id, 'category_color', sanitize_hex_color( $_POST['category_color'] ) );
        }
    }
}
