<?php
/**
 * Plugin Name: Premium Prompts Library
 * Plugin URI: Informationsystems.io
 * Description: A comprehensive prompts library system for WordPress Multisite with elegant UI and chatbot integration
 * Version: 1.0.0
 * Author: Information Systems
 * Author URI: Informationsystems.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prompts-library
 * Domain Path: /languages
 * Network: true
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'PROMPTS_LIBRARY_VERSION', '1.0.0' );
define( 'PROMPTS_LIBRARY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PROMPTS_LIBRARY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PROMPTS_LIBRARY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Plugin Class
 */
class Prompts_Library {

    /**
     * Single instance of the class
     *
     * @var Prompts_Library
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library
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
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files
     */
    private function includes() {
        require_once PROMPTS_LIBRARY_PLUGIN_DIR . 'includes/class-post-type.php';
        require_once PROMPTS_LIBRARY_PLUGIN_DIR . 'includes/class-taxonomy.php';
        require_once PROMPTS_LIBRARY_PLUGIN_DIR . 'includes/class-admin-menu.php';
        require_once PROMPTS_LIBRARY_PLUGIN_DIR . 'includes/class-frontend.php';
        require_once PROMPTS_LIBRARY_PLUGIN_DIR . 'includes/class-ajax-handler.php';
        require_once PROMPTS_LIBRARY_PLUGIN_DIR . 'includes/class-settings.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'plugins_loaded', array( $this, 'bootstrap_components' ) );
        add_action( 'admin_init', array( $this, 'maybe_assign_capabilities' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'prompts-library', false, dirname( PROMPTS_LIBRARY_PLUGIN_BASENAME ) . '/languages' );
    }

    /**
     * Initialize plugin components.
     */
    public function bootstrap_components() {
        Prompts_Library_Post_Type::get_instance();
        Prompts_Library_Taxonomy::get_instance();
        Prompts_Library_Admin_Menu::get_instance();
        Prompts_Library_Frontend::get_instance();
        Prompts_Library_Ajax_Handler::get_instance();
        Prompts_Library_Settings::get_instance();
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create custom post type
        Prompts_Library_Post_Type::register_post_type();
        Prompts_Library_Taxonomy::register_taxonomies();

        // Flush rewrite rules
        flush_rewrite_rules();

        $this->assign_capabilities();

        // Set default settings
        if ( ! get_site_option( 'prompts_library_settings' ) ) {
            update_site_option( 'prompts_library_settings', array(
                'prompts_per_page' => 9,
                'cards_per_row' => 3,
            ) );
        }
    }

    /**
     * Grant the custom capabilities to the administrator role on the main site.
     */
    private function assign_capabilities() {
        $main_site_id = function_exists( 'get_main_site_id' ) ? get_main_site_id() : get_current_blog_id();
        $switched     = false;

        if ( is_multisite() && get_current_blog_id() !== $main_site_id ) {
            switch_to_blog( $main_site_id );
            $switched = true;
        }

        $role = get_role( 'administrator' );

        if ( $role ) {
            foreach ( Prompts_Library_Post_Type::get_capability_map() as $capability ) {
                $role->add_cap( $capability );
            }

            foreach ( Prompts_Library_Taxonomy::get_capabilities() as $capability ) {
                $role->add_cap( $capability );
            }
        }

        if ( $switched ) {
            restore_current_blog();
        }
    }

    /**
     * Ensure capabilities remain assigned after updates.
     */
    public function maybe_assign_capabilities() {
        // Only run in the network admin or the main site context to avoid unnecessary role switching.
        if ( is_multisite() && ! is_network_admin() && ! is_main_site() ) {
            return;
        }

        $this->assign_capabilities();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function prompts_library_init() {
    return Prompts_Library::get_instance();
}

// Start the plugin
prompts_library_init();
