<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PL_Ajax_Handler {
    public function __construct() {
        add_action( 'wp_ajax_pl_fetch_prompts', array( $this, 'fetch_prompts' ) );
        add_action( 'wp_ajax_nopriv_pl_fetch_prompts', array( $this, 'fetch_prompts' ) );
    }

    public function fetch_prompts() {
        check_ajax_referer( 'pl_fetch', 'nonce' );

        $category = isset($_POST['category']) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
        $tag      = isset($_POST['tag'])      ? sanitize_text_field( wp_unslash( $_POST['tag'] ) )      : '';

        $main = function_exists('get_main_site_id') ? get_main_site_id() : 1;
        switch_to_blog( $main );

        $taxq = array();
        if ( $category ) {
            $taxq[] = array(
                'taxonomy' => 'prompt_category',
                'field'    => 'slug',
                'terms'    => $category,
            );
        }
        if ( $tag ) {
            $taxq[] = array(
                'taxonomy' => 'prompt_tag',
                'field'    => 'slug',
                'terms'    => $tag,
            );
        }

        $q = new WP_Query( array(
            'post_type'      => 'prompt',
            'posts_per_page' => 9,
            'post_status'    => 'publish',
            'tax_query'      => $taxq,
        ) );

        $cards = array();
        if ( $q->have_posts() ) {
            while ( $q->have_posts() ) {
                $q->the_post();
                $cards[] = array(
                    'title' => get_the_title(),
                    'excerpt' => wp_trim_words( get_the_content(), 30 ),
                    'link'  => get_permalink(),
                );
            }
            wp_reset_postdata();
        }

        restore_current_blog();

        wp_send_json_success( array( 'cards' => $cards ) );
    }
}
