<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PL_Taxonomy {

    public static function register() {
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 10 );
    }

    public static function register_taxonomies() {
        $is_main = function_exists('get_main_site_id')
            ? ( get_current_blog_id() === get_main_site_id() )
            : is_main_site();

        if ( ! $is_main ) {
            // Still attach taxonomies to CPT for queries, but hide UI
            $cat_args = array(
                'hierarchical'      => true,
                'labels'            => array( 'name' => __( 'Prompt Categories','prompts-library' ) ),
                'show_ui'           => false,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => false,
            );
            $tag_args = array(
                'hierarchical'      => false,
                'labels'            => array( 'name' => __( 'Prompt Tags','prompts-library' ) ),
                'show_ui'           => false,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => false,
            );
            register_taxonomy( 'prompt_category', array( 'prompt' ), $cat_args );
            register_taxonomy( 'prompt_tag',      array( 'prompt' ), $tag_args );
            return;
        }

        $cat_args = array(
            'hierarchical'      => true,
            'labels'            => array( 'name' => __( 'Prompt Categories','prompts-library' ) ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'prompt-category' ),
        );

        $tag_args = array(
            'hierarchical'      => false,
            'labels'            => array( 'name' => __( 'Prompt Tags','prompts-library' ) ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'prompt-tag' ),
        );

        register_taxonomy( 'prompt_category', array( 'prompt' ), $cat_args );
        register_taxonomy( 'prompt_tag',      array( 'prompt' ), $tag_args );
    }
}
