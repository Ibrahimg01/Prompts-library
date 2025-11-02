<?php
/**
 * Plugin Name: Premium Prompts Library
 * Plugin URI: Informationsystems.io
 * Description: A comprehensive prompts library system for WordPress Multisite with elegant UI and chatbot integration
 * Version: 1.0.0
 * Author: Learn With Hasan
 * Author URI: Information Systems
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
        add_action( 'init', array( $this, 'init' ) );
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
     * Initialize plugin
     */
    public function init() {
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

        // Set default settings
        if ( ! get_site_option( 'prompts_library_settings' ) ) {
            update_site_option( 'prompts_library_settings', array(
                'prompts_per_page' => 9,
                'cards_per_row' => 3,
            ) );
        }
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
