<?php
/**
 * AJAX Handler
 *
 * @package Prompts_Library
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * AJAX Handler Class
 */
class Prompts_Library_Ajax_Handler {

    /**
     * Single instance
     *
     * @var Prompts_Library_Ajax_Handler
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library_Ajax_Handler
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
        add_action( 'wp_ajax_get_prompt_details', array( $this, 'get_prompt_details' ) );
    }

    /**
     * Get prompt details via AJAX
     */
    public function get_prompt_details() {
        check_ajax_referer( 'prompts_library_nonce', 'nonce' );

        if ( ! isset( $_POST['prompt_id'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Prompt ID is required', 'prompts-library' ) ) );
        }

        $prompt_id = absint( $_POST['prompt_id'] );

        // Switch to main site to get the prompt
        switch_to_blog( get_main_site_id() );
        
        $prompt = get_post( $prompt_id );

        if ( ! $prompt || 'prompt' !== $prompt->post_type ) {
            restore_current_blog();
            wp_send_json_error( array( 'message' => __( 'Prompt not found', 'prompts-library' ) ) );
        }

        $description = get_post_meta( $prompt_id, '_prompt_description', true );
        $prompt_text = get_post_meta( $prompt_id, '_prompt_text', true );
        $categories = get_the_terms( $prompt_id, 'prompt_category' );

        $category_data = array();
        if ( $categories && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $cat ) {
                $cat_color = get_term_meta( $cat->term_id, 'category_color', true );
                if ( empty( $cat_color ) ) {
                    $cat_color = '#8b5cf6';
                }
                $category_data[] = array(
                    'name' => $cat->name,
                    'color' => $cat_color,
                );
            }
        }

        restore_current_blog();

        wp_send_json_success( array(
            'title' => $prompt->post_title,
            'description' => $description,
            'prompt_text' => $prompt_text,
            'categories' => $category_data,
        ) );
    }
}
