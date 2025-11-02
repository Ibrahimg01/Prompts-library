<?php
/**
 * Plugin Name: Prompts Library
 * Description: Multisite prompts library: manage on main site, view on subsites, settings in Network Admin.
 * Version: 1.0.6
 * Author: Information Systems
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PL_DIR', plugin_dir_path( __FILE__ ) );
define( 'PL_URL', plugin_dir_url( __FILE__ ) );
define( 'PL_VER', '1.0.6' );

require_once PL_DIR . 'includes/class-post-type.php';
require_once PL_DIR . 'includes/class-taxonomy.php';
require_once PL_DIR . 'includes/class-admin-menu.php';
require_once PL_DIR . 'includes/class-frontend.php';
require_once PL_DIR . 'includes/class-ajax-handler.php';
require_once PL_DIR . 'includes/class-settings.php';

final class Prompts_Library_Plugin {
    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'init' ), 5 );
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
    }

    public function init() {
        // Register CPT + taxonomies on init.
        PL_Post_Type::register();
        PL_Taxonomy::register();
    }

    public function plugins_loaded() {
        // Admin UI
        if ( is_admin() ) {
            new PL_Admin_Menu();
        }
        // Frontend shortcodes/assets
        new PL_Frontend();

        // AJAX endpoints
        new PL_Ajax_Handler();

        // Settings page (Network Admin)
        new PL_Settings();
    }
}

function prompts_library() {
    return Prompts_Library_Plugin::instance();
}
prompts_library();
