<?php
/**
 * Admin Menu Handler
 *
 * @package Prompts_Library
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Menu Class
 */
class Prompts_Library_Admin_Menu {

    /**
     * Single instance
     *
     * @var Prompts_Library_Admin_Menu
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library_Admin_Menu
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
        if ( is_main_site() ) {
            add_action( 'network_admin_menu', array( $this, 'add_network_admin_menu' ) );
        } else {
            add_action( 'admin_menu', array( $this, 'add_subsite_admin_menu' ) );
        }
    }

    /**
     * Add network admin menu (Super Admin)
     */
    public function add_network_admin_menu() {
        add_menu_page(
            __( 'Prompts Library', 'prompts-library' ),
            __( 'Prompts Library', 'prompts-library' ),
            'manage_network',
            'prompts-library',
            array( $this, 'render_admin_page' ),
            'dashicons-editor-quote',
            30
        );

        add_submenu_page(
            'prompts-library',
            __( 'All Prompts', 'prompts-library' ),
            __( 'All Prompts', 'prompts-library' ),
            'manage_network',
            'edit.php?post_type=prompt'
        );

        add_submenu_page(
            'prompts-library',
            __( 'Add New', 'prompts-library' ),
            __( 'Add New', 'prompts-library' ),
            'manage_network',
            'post-new.php?post_type=prompt'
        );

        add_submenu_page(
            'prompts-library',
            __( 'Categories', 'prompts-library' ),
            __( 'Categories', 'prompts-library' ),
            'manage_network',
            'edit-tags.php?taxonomy=prompt_category&post_type=prompt'
        );

        add_submenu_page(
            'prompts-library',
            __( 'Tags', 'prompts-library' ),
            __( 'Tags', 'prompts-library' ),
            'manage_network',
            'edit-tags.php?taxonomy=prompt_tag&post_type=prompt'
        );

        add_submenu_page(
            'prompts-library',
            __( 'Settings', 'prompts-library' ),
            __( 'Settings', 'prompts-library' ),
            'manage_network',
            'prompts-library-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Add subsite admin menu (Tenant Admin)
     */
    public function add_subsite_admin_menu() {
        add_menu_page(
            __( 'Prompts Library', 'prompts-library' ),
            __( 'Prompts Library', 'prompts-library' ),
            'edit_posts',
            'prompts-library-view',
            array( $this, 'render_frontend_page' ),
            'dashicons-editor-quote',
            30
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Prompts Library - Super Admin', 'prompts-library' ); ?></h1>
            <p><?php esc_html_e( 'Manage all prompts, categories, and tags from the submenus. You can publish prompts to specific subsites when editing each prompt.', 'prompts-library' ); ?></p>
            
            <div class="prompts-dashboard">
                <?php
                $prompts_count = wp_count_posts( 'prompt' );
                $categories_count = wp_count_terms( array( 'taxonomy' => 'prompt_category' ) );
                $tags_count = wp_count_terms( array( 'taxonomy' => 'prompt_tag' ) );
                ?>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
                    <div style="background: #fff; padding: 20px; border-left: 4px solid #8b5cf6; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; color: #0D0D2B;"><?php esc_html_e( 'Total Prompts', 'prompts-library' ); ?></h3>
                        <p style="font-size: 32px; font-weight: 700; margin: 0; color: #8b5cf6;">
                            <?php echo esc_html( $prompts_count->publish + $prompts_count->draft ); ?>
                        </p>
                    </div>
                    <div style="background: #fff; padding: 20px; border-left: 4px solid #f65c4b; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; color: #0D0D2B;"><?php esc_html_e( 'Categories', 'prompts-library' ); ?></h3>
                        <p style="font-size: 32px; font-weight: 700; margin: 0; color: #f65c4b;">
                            <?php echo esc_html( $categories_count ); ?>
                        </p>
                    </div>
                    <div style="background: #fff; padding: 20px; border-left: 4px solid #0D0D2B; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 10px 0; color: #0D0D2B;"><?php esc_html_e( 'Tags', 'prompts-library' ); ?></h3>
                        <p style="font-size: 32px; font-weight: 700; margin: 0; color: #0D0D2B;">
                            <?php echo esc_html( $tags_count ); ?>
                        </p>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <h2><?php esc_html_e( 'Quick Actions', 'prompts-library' ); ?></h2>
                    <a href="<?php echo esc_url( network_admin_url( 'post-new.php?post_type=prompt' ) ); ?>" class="button button-primary button-large">
                        <?php esc_html_e( 'Add New Prompt', 'prompts-library' ); ?>
                    </a>
                    <a href="<?php echo esc_url( network_admin_url( 'edit.php?post_type=prompt' ) ); ?>" class="button button-large">
                        <?php esc_html_e( 'View All Prompts', 'prompts-library' ); ?>
                    </a>
                    <a href="<?php echo esc_url( network_admin_url( 'edit-tags.php?taxonomy=prompt_category&post_type=prompt' ) ); ?>" class="button button-large">
                        <?php esc_html_e( 'Manage Categories', 'prompts-library' ); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if ( isset( $_POST['prompts_library_settings_nonce'] ) && wp_verify_nonce( $_POST['prompts_library_settings_nonce'], 'prompts_library_settings' ) ) {
            $settings = array(
                'prompts_per_page' => isset( $_POST['prompts_per_page'] ) ? absint( $_POST['prompts_per_page'] ) : 9,
                'cards_per_row' => isset( $_POST['cards_per_row'] ) ? absint( $_POST['cards_per_row'] ) : 3,
            );
            update_site_option( 'prompts_library_settings', $settings );
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved successfully!', 'prompts-library' ) . '</p></div>';
        }

        $settings = get_site_option( 'prompts_library_settings', array(
            'prompts_per_page' => 9,
            'cards_per_row' => 3,
        ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Prompts Library Settings', 'prompts-library' ); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'prompts_library_settings', 'prompts_library_settings_nonce' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="prompts_per_page"><?php esc_html_e( 'Prompts Per Page', 'prompts-library' ); ?></label>
                        </th>
                        <td>
                            <input 
                                type="number" 
                                name="prompts_per_page" 
                                id="prompts_per_page" 
                                value="<?php echo esc_attr( $settings['prompts_per_page'] ); ?>" 
                                min="3" 
                                max="100" 
                                class="small-text"
                            />
                            <p class="description"><?php esc_html_e( 'Number of prompt cards to display per page', 'prompts-library' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cards_per_row"><?php esc_html_e( 'Cards Per Row', 'prompts-library' ); ?></label>
                        </th>
                        <td>
                            <select name="cards_per_row" id="cards_per_row">
                                <option value="2" <?php selected( $settings['cards_per_row'], 2 ); ?>>2</option>
                                <option value="3" <?php selected( $settings['cards_per_row'], 3 ); ?>>3</option>
                                <option value="4" <?php selected( $settings['cards_per_row'], 4 ); ?>>4</option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Number of cards to display per row', 'prompts-library' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render frontend page for tenant admins
     */
    public function render_frontend_page() {
        // This will be handled by the frontend class
        Prompts_Library_Frontend::render_library_page();
    }
}
