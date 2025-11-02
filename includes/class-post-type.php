<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PL_Post_Type {

    public static function register() {
        add_action( 'init', array( __CLASS__, 'register_post_type' ), 9 );
    }

    public static function register_post_type() {
        $labels = array(
            'name'               => _x( 'Prompts', 'post type general name', 'prompts-library' ),
            'singular_name'      => _x( 'Prompt', 'post type singular name', 'prompts-library' ),
            'menu_name'          => _x( 'Prompts', 'admin menu', 'prompts-library' ),
            'name_admin_bar'     => _x( 'Prompt', 'add new on admin bar', 'prompts-library' ),
            'add_new'            => _x( 'Add New', 'prompt', 'prompts-library' ),
            'add_new_item'       => __( 'Add New Prompt', 'prompts-library' ),
            'new_item'           => __( 'New Prompt', 'prompts-library' ),
            'edit_item'          => __( 'Edit Prompt', 'prompts-library' ),
            'view_item'          => __( 'View Prompt', 'prompts-library' ),
            'all_items'          => __( 'All Prompts', 'prompts-library' ),
            'search_items'       => __( 'Search Prompts', 'prompts-library' ),
            'not_found'          => __( 'No prompts found.', 'prompts-library' ),
            'not_found_in_trash' => __( 'No prompts found in Trash.', 'prompts-library' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'capability_type'    => 'post',
            'supports'           => array( 'title', 'editor', 'excerpt' ),
            'menu_icon'          => 'dashicons-editor-quote',
            'menu_position'      => 26,
        );

        // Only expose UI on the main site; subsites keep viewer-only.
        $is_main = function_exists('get_main_site_id')
            ? ( get_current_blog_id() === get_main_site_id() )
            : is_main_site();

        if ( ! $is_main ) {
            $args['show_ui']      = false;
            $args['show_in_menu'] = false;
        }

        register_post_type( 'prompt', $args );
    }
}
