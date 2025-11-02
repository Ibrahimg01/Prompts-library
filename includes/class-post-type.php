<?php
/**
 * Custom Post Type Handler
 *
 * @package Prompts_Library
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Post Type Class
 */
class Prompts_Library_Post_Type {

    /**
     * Single instance
     *
     * @var Prompts_Library_Post_Type
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library_Post_Type
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
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_prompt', array( $this, 'save_meta_boxes' ), 10, 2 );
        add_filter( 'manage_prompt_posts_columns', array( $this, 'set_custom_columns' ) );
        add_action( 'manage_prompt_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
    }

    /**
     * Retrieve the capability map used by the post type.
     *
     * @return array
     */
    public static function get_capability_map() {
        return array(
            'edit_post'              => 'edit_prompt',
            'read_post'              => 'read_prompt',
            'delete_post'            => 'delete_prompt',
            'edit_posts'             => 'edit_prompts',
            'edit_others_posts'      => 'edit_others_prompts',
            'publish_posts'          => 'publish_prompts',
            'read_private_posts'     => 'read_private_prompts',
            'delete_posts'           => 'delete_prompts',
            'delete_private_posts'   => 'delete_private_prompts',
            'delete_published_posts' => 'delete_published_prompts',
            'delete_others_posts'    => 'delete_others_prompts',
            'edit_private_posts'     => 'edit_private_prompts',
            'edit_published_posts'   => 'edit_published_prompts',
            'create_posts'           => 'create_prompts',
        );
    }

    /**
     * Register custom post type
     */
    public static function register_post_type() {
        $is_main_site = is_main_site();

        $labels = array(
            'name'                  => _x( 'Prompts', 'Post Type General Name', 'prompts-library' ),
            'singular_name'         => _x( 'Prompt', 'Post Type Singular Name', 'prompts-library' ),
            'menu_name'             => __( 'Prompts Library', 'prompts-library' ),
            'name_admin_bar'        => __( 'Prompt', 'prompts-library' ),
            'archives'              => __( 'Prompt Archives', 'prompts-library' ),
            'attributes'            => __( 'Prompt Attributes', 'prompts-library' ),
            'parent_item_colon'     => __( 'Parent Prompt:', 'prompts-library' ),
            'all_items'             => __( 'All Prompts', 'prompts-library' ),
            'add_new_item'          => __( 'Add New Prompt', 'prompts-library' ),
            'add_new'               => __( 'Add New', 'prompts-library' ),
            'new_item'              => __( 'New Prompt', 'prompts-library' ),
            'edit_item'             => __( 'Edit Prompt', 'prompts-library' ),
            'update_item'           => __( 'Update Prompt', 'prompts-library' ),
            'view_item'             => __( 'View Prompt', 'prompts-library' ),
            'view_items'            => __( 'View Prompts', 'prompts-library' ),
            'search_items'          => __( 'Search Prompt', 'prompts-library' ),
            'not_found'             => __( 'Not found', 'prompts-library' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'prompts-library' ),
            'featured_image'        => __( 'Featured Image', 'prompts-library' ),
            'set_featured_image'    => __( 'Set featured image', 'prompts-library' ),
            'remove_featured_image' => __( 'Remove featured image', 'prompts-library' ),
            'use_featured_image'    => __( 'Use as featured image', 'prompts-library' ),
            'insert_into_item'      => __( 'Insert into prompt', 'prompts-library' ),
            'uploaded_to_this_item' => __( 'Uploaded to this prompt', 'prompts-library' ),
            'items_list'            => __( 'Prompts list', 'prompts-library' ),
            'items_list_navigation' => __( 'Prompts list navigation', 'prompts-library' ),
            'filter_items_list'     => __( 'Filter prompts list', 'prompts-library' ),
        );

        $args = array(
            'label'                 => __( 'Prompt', 'prompts-library' ),
            'description'           => __( 'Prompts Library', 'prompts-library' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'page-attributes' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => $is_main_site,
            'show_in_menu'          => false,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => array( 'prompt', 'prompts' ),
            'map_meta_cap'          => true,
            'capabilities'          => self::get_capability_map(),
            'show_in_rest'          => false,
            'menu_icon'             => 'dashicons-editor-quote',
        );

        register_post_type( 'prompt', $args );
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'prompt_details',
            __( 'Prompt Details', 'prompts-library' ),
            array( $this, 'render_prompt_details_meta_box' ),
            'prompt',
            'normal',
            'high'
        );

        add_meta_box(
            'prompt_description',
            __( 'Short Description', 'prompts-library' ),
            array( $this, 'render_description_meta_box' ),
            'prompt',
            'normal',
            'high'
        );

        if ( is_main_site() ) {
            add_meta_box(
                'prompt_publish_sites',
                __( 'Publish to Sites', 'prompts-library' ),
                array( $this, 'render_publish_sites_meta_box' ),
                'prompt',
                'side',
                'default'
            );
        }
    }

    /**
     * Render prompt details meta box
     */
    public function render_prompt_details_meta_box( $post ) {
        wp_nonce_field( 'prompt_meta_box', 'prompt_meta_box_nonce' );
        $prompt_text = get_post_meta( $post->ID, '_prompt_text', true );
        ?>
        <div class="prompt-meta-box">
            <p>
                <label for="prompt_text" style="font-weight: 600; display: block; margin-bottom: 5px;">
                    <?php esc_html_e( 'Prompt Text:', 'prompts-library' ); ?>
                </label>
                <textarea 
                    name="prompt_text" 
                    id="prompt_text" 
                    rows="12" 
                    style="width: 100%; font-family: monospace; padding: 10px;"
                    placeholder="<?php esc_attr_e( 'Enter the full prompt here...', 'prompts-library' ); ?>"
                ><?php echo esc_textarea( $prompt_text ); ?></textarea>
            </p>
            <p class="description">
                <?php esc_html_e( 'This is the actual prompt that will be used in the chatbot.', 'prompts-library' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Render description meta box
     */
    public function render_description_meta_box( $post ) {
        $description = get_post_meta( $post->ID, '_prompt_description', true );
        ?>
        <div class="description-meta-box">
            <p>
                <textarea 
                    name="prompt_description" 
                    id="prompt_description" 
                    rows="3" 
                    style="width: 100%; padding: 10px;"
                    placeholder="<?php esc_attr_e( 'Brief description shown on the card (1-2 sentences)...', 'prompts-library' ); ?>"
                ><?php echo esc_textarea( $description ); ?></textarea>
            </p>
            <p class="description">
                <?php esc_html_e( 'This short description will appear on the prompt card in the library.', 'prompts-library' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Render publish sites meta box
     */
    public function render_publish_sites_meta_box( $post ) {
        $all    = (int) get_post_meta( $post->ID, 'pl_publish_all', true );
        $chosen = get_post_meta( $post->ID, 'pl_publish_sites', true );

        if ( ! is_array( $chosen ) ) {
            $chosen = array();
        }

        if ( empty( $chosen ) ) {
            $legacy = get_post_meta( $post->ID, '_published_sites', true );
            if ( is_array( $legacy ) && ! empty( $legacy ) ) {
                $chosen = array_values( array_map( 'absint', $legacy ) );
            }
        }

        $sites = get_sites( array( 'number' => 0 ) );
        ?>
        <p style="margin-bottom:8px;">
            <label>
                <input type="checkbox" name="pl_publish_all" value="1" <?php checked( $all, 1 ); ?>>
                <strong><?php esc_html_e( 'Publish to ALL sites', 'prompts-library' ); ?></strong>
            </label>
        </p>

        <div id="pl-sites-list" style="<?php echo $all ? 'opacity:.5;pointer-events:none;' : ''; ?>">
            <?php foreach ( $sites as $site ) :
                $bid   = (int) $site->blog_id;
                $label = esc_html( $site->domain . $site->path );
                ?>
                <label style="display:block;margin:4px 0;">
                    <input
                        type="checkbox"
                        name="pl_publish_sites[]"
                        value="<?php echo esc_attr( $bid ); ?>"
                        <?php checked( in_array( $bid, $chosen, true ) ); ?>
                    >
                    <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <?php wp_nonce_field( 'pl_save_publish_sites', 'pl_publish_sites_nonce' ); ?>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
          const all = document.querySelector('input[name="pl_publish_all"]');
          const box = document.getElementById('pl-sites-list');
          if (!all || !box) return;
          function toggle() {
            if (all.checked) { box.style.opacity = .5; box.style.pointerEvents = 'none'; }
            else { box.style.opacity = 1; box.style.pointerEvents = ''; }
          }
          all.addEventListener('change', toggle);
          toggle();
        });
        </script>
        <?php
    }

    /**
     * Save meta boxes
     */
    public function save_meta_boxes( $post_id, $post ) {
        // Check nonce
        if ( ! isset( $_POST['prompt_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['prompt_meta_box_nonce'], 'prompt_meta_box' ) ) {
            return;
        }

        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save prompt text
        if ( isset( $_POST['prompt_text'] ) ) {
            update_post_meta( $post_id, '_prompt_text', wp_kses_post( $_POST['prompt_text'] ) );
        }

        // Save description
        if ( isset( $_POST['prompt_description'] ) ) {
            update_post_meta( $post_id, '_prompt_description', sanitize_textarea_field( $_POST['prompt_description'] ) );
        }

        // Save publish-to-sites settings (only on main site)
        if ( is_main_site() ) {
            $nonce = isset( $_POST['pl_publish_sites_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['pl_publish_sites_nonce'] ) ) : '';

            if ( ! empty( $nonce ) && wp_verify_nonce( $nonce, 'pl_save_publish_sites' ) ) {
                $all = ! empty( $_POST['pl_publish_all'] ) ? 1 : 0;
                update_post_meta( $post_id, 'pl_publish_all', $all );

                $sites = array();
                if ( ! $all && isset( $_POST['pl_publish_sites'] ) ) {
                    $sites = array_map( 'absint', (array) wp_unslash( $_POST['pl_publish_sites'] ) );
                    $sites = array_values( array_unique( array_filter( $sites ) ) );
                }

                update_post_meta( $post_id, 'pl_publish_sites', $sites );

                // Maintain legacy meta for backward compatibility with older versions.
                update_post_meta( $post_id, '_published_sites', $all ? array() : $sites );
            }
        }
    }

    /**
     * Set custom columns
     */
    public function set_custom_columns( $columns ) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['prompt_category'] = __( 'Category', 'prompts-library' );
        $new_columns['prompt_tags'] = __( 'Tags', 'prompts-library' );
        
        if ( is_main_site() ) {
            $new_columns['published_sites'] = __( 'Published Sites', 'prompts-library' );
        }
        
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    /**
     * Custom column content
     */
    public function custom_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'prompt_category':
                $terms = get_the_terms( $post_id, 'prompt_category' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $term_names = wp_list_pluck( $terms, 'name' );
                    echo esc_html( implode( ', ', $term_names ) );
                } else {
                    echo '—';
                }
                break;

            case 'prompt_tags':
                $terms = get_the_terms( $post_id, 'prompt_tag' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $term_names = wp_list_pluck( $terms, 'name' );
                    echo esc_html( implode( ', ', $term_names ) );
                } else {
                    echo '—';
                }
                break;

            case 'published_sites':
                $all = (int) get_post_meta( $post_id, 'pl_publish_all', true );

                if ( 1 === $all ) {
                    esc_html_e( 'All sites', 'prompts-library' );
                    break;
                }

                $published_sites = get_post_meta( $post_id, 'pl_publish_sites', true );

                if ( ! is_array( $published_sites ) || empty( $published_sites ) ) {
                    $published_sites = get_post_meta( $post_id, '_published_sites', true );
                }

                if ( is_array( $published_sites ) && ! empty( $published_sites ) ) {
                    echo esc_html( count( $published_sites ) ) . ' ' . esc_html__( 'sites', 'prompts-library' );
                } else {
                    echo '—';
                }
                break;
        }
    }
}
