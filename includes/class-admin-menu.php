<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PL_Admin_Menu {

    public function __construct() {
        add_action( 'network_admin_menu', array( $this, 'add_network_admin_menu' ) ); // Super Admin
        add_action( 'admin_menu', array( $this, 'add_subsite_admin_menu' ) );        // Site admins
    }

    /**
     * Redirect to a specific admin path on the MAIN site.
     * $path like 'edit.php?post_type=prompt'
     */
    protected function redirect_to_main_site_admin( $path ) {
        $main_id = function_exists('get_main_site_id') ? get_main_site_id() : 1;
        $url = get_admin_url( $main_id, ltrim( $path, '/' ) );
        wp_safe_redirect( $url );
        exit;
    }

    /**
     * Network Admin (Super Admin): section + links that open main site editors.
     */
    public function add_network_admin_menu() {
        if ( ! is_multisite() || ! is_network_admin() ) return;

        add_menu_page(
            __( 'Prompts Library', 'prompts-library' ),
            __( 'Prompts Library', 'prompts-library' ),
            'manage_network',
            'prompts-library',
            array( $this, 'render_network_dashboard' ),
            'dashicons-editor-quote',
            60
        );

        add_submenu_page(
            'prompts-library',
            __( 'All Prompts', 'prompts-library' ),
            __( 'All Prompts', 'prompts-library' ),
            'manage_network',
            'prompts-library-all',
            array( $this, 'render_network_all_prompts' )
        );

        add_submenu_page(
            'prompts-library',
            __( 'Add New', 'prompts-library' ),
            __( 'Add New', 'prompts-library' ),
            'manage_network',
            'prompts-library-add',
            array( $this, 'render_network_add_new' )
        );

        add_submenu_page(
            'prompts-library',
            __( 'Categories', 'prompts-library' ),
            __( 'Categories', 'prompts-library' ),
            'manage_network',
            'prompts-library-categories',
            array( $this, 'render_network_categories' )
        );

        add_submenu_page(
            'prompts-library',
            __( 'Tags', 'prompts-library' ),
            __( 'Tags', 'prompts-library' ),
            'manage_network',
            'prompts-library-tags',
            array( $this, 'render_network_tags' )
        );

        add_submenu_page(
            'prompts-library',
            __( 'Settings', 'prompts-library' ),
            __( 'Settings', 'prompts-library' ),
            'manage_network_options',
            'prompts-library-settings',
            array( $this, 'render_network_settings' )
        );
    }

    public function render_network_dashboard() {
        // Counters (safe)
        $prompts_count = wp_count_posts( 'prompt' );
        $categories_raw = wp_count_terms( array( 'taxonomy' => 'prompt_category' ) );
        $tags_raw       = wp_count_terms( array( 'taxonomy' => 'prompt_tag' ) );

        $categories_count = is_wp_error( $categories_raw ) ? 0 : (int) $categories_raw;
        $tags_count       = is_wp_error( $tags_raw )       ? 0 : (int) $tags_raw;

        $pl_drafts    = isset( $prompts_count->draft )   ? (int) $prompts_count->draft   : 0;
        $pl_published = isset( $prompts_count->publish ) ? (int) $prompts_count->publish : 0;

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Prompts Library - Super Admin', 'prompts-library' ); ?></h1>

            <div style="display:flex; gap:24px; margin:24px 0;">
                <div style="flex:1; padding:16px; border:1px solid #eee;">
                    <h3><?php esc_html_e('Total Prompts','prompts-library'); ?></h3>
                    <div style="font-size:32px;"><?php echo esc_html( $pl_drafts + $pl_published ); ?></div>
                </div>
                <div style="flex:1; padding:16px; border:1px solid #eee;">
                    <h3><?php esc_html_e('Categories','prompts-library'); ?></h3>
                    <div style="font-size:32px;"><?php echo esc_html( $categories_count ); ?></div>
                </div>
                <div style="flex:1; padding:16px; border:1px solid #eee;">
                    <h3><?php esc_html_e('Tags','prompts-library'); ?></h3>
                    <div style="font-size:32px;"><?php echo esc_html( $tags_count ); ?></div>
                </div>
            </div>

            <h2><?php esc_html_e('Quick Actions','prompts-library'); ?></h2>
            <p>
                <a href="<?php echo esc_url( get_admin_url( get_main_site_id(), 'post-new.php?post_type=prompt' ) ); ?>" class="button button-primary"><?php esc_html_e('Add New Prompt','prompts-library'); ?></a>
                <a href="<?php echo esc_url( get_admin_url( get_main_site_id(), 'edit.php?post_type=prompt' ) ); ?>" class="button"><?php esc_html_e('View All Prompts','prompts-library'); ?></a>
                <a href="<?php echo esc_url( get_admin_url( get_main_site_id(), 'edit-tags.php?taxonomy=prompt_category&post_type=prompt' ) ); ?>" class="button"><?php esc_html_e('Manage Categories','prompts-library'); ?></a>
            </p>
        </div>
        <?php
    }

    public function render_network_all_prompts()   { $this->redirect_to_main_site_admin( 'edit.php?post_type=prompt' ); }
    public function render_network_add_new()       { $this->redirect_to_main_site_admin( 'post-new.php?post_type=prompt' ); }
    public function render_network_categories()    { $this->redirect_to_main_site_admin( 'edit-tags.php?taxonomy=prompt_category&post_type=prompt' ); }
    public function render_network_tags()          { $this->redirect_to_main_site_admin( 'edit-tags.php?taxonomy=prompt_tag&post_type=prompt' ); }

    public function render_network_settings() {
        // just reuse PL_Settings page renderer
        PL_Settings::render_page();
    }

    /**
     * Site Admin menu. Main Site: management links present. Subsites: Viewer only.
     */
    public function add_subsite_admin_menu() {
        if ( is_network_admin() ) return;

        add_menu_page(
            __( 'Prompts Library', 'prompts-library' ),
            __( 'Prompts Library', 'prompts-library' ),
            'read',
            'prompts-library-view',
            array( $this, 'render_subsite_view' ),
            'dashicons-editor-quote',
            60
        );

        // On main site admin, add links to core CPT editors for convenience.
        if ( is_main_site() ) {
            add_submenu_page( 'prompts-library-view', __( 'All Prompts','prompts-library' ), __( 'All Prompts','prompts-library' ), 'edit_posts', 'pl-manage',
                function() { wp_safe_redirect( admin_url( 'edit.php?post_type=prompt' ) ); exit; } );
            add_submenu_page( 'prompts-library-view', __( 'Add New','prompts-library' ), __( 'Add New','prompts-library' ), 'edit_posts', 'pl-add',
                function() { wp_safe_redirect( admin_url( 'post-new.php?post_type=prompt' ) ); exit; } );
            add_submenu_page( 'prompts-library-view', __( 'Categories','prompts-library' ), __( 'Categories','prompts-library' ), 'manage_categories', 'pl-cats',
                function() { wp_safe_redirect( admin_url( 'edit-tags.php?taxonomy=prompt_category&post_type=prompt' ) ); exit; } );
            add_submenu_page( 'prompts-library-view', __( 'Tags','prompts-library' ), __( 'Tags','prompts-library' ), 'manage_categories', 'pl-tags',
                function() { wp_safe_redirect( admin_url( 'edit-tags.php?taxonomy=prompt_tag&post_type=prompt' ) ); exit; } );
        }
    }

    public function render_subsite_view() {
        echo '<div class="wrap"><h1>'.esc_html__('Prompts Library','prompts-library').'</h1>';
        echo do_shortcode('[prompts_library]');
        echo '</div>';
    }
}
