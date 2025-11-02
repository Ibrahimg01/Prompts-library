<?php
/**
 * Settings Handler
 *
 * @package Prompts_Library
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Settings Class
 */
class Prompts_Library_Settings {

    /**
     * Single instance
     *
     * @var Prompts_Library_Settings
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library_Settings
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
        // Settings are handled in the admin menu class
    }

    /**
     * Get setting
     *
     * @param string $key Setting key.
     * @param mixed  $default Default value.
     * @return mixed
     */
    public static function get_setting( $key, $default = '' ) {
        $settings = get_site_option( 'prompts_library_settings', array() );
        return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
    }

    /**
     * Update setting
     *
     * @param string $key Setting key.
     * @param mixed  $value Setting value.
     */
    public static function update_setting( $key, $value ) {
        $settings = get_site_option( 'prompts_library_settings', array() );
        $settings[ $key ] = $value;
        update_site_option( 'prompts_library_settings', $settings );
    }
}
