<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PL_Frontend {

    public function __construct() {
        add_shortcode( 'prompts_library', array( $this, 'shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
    }

    public function assets() {
        wp_register_style( 'pl-frontend', PL_URL . 'assets/css/frontend.css', array(), PL_VER );
        wp_register_script( 'pl-frontend', PL_URL . 'assets/js/frontend.js', array('jquery'), PL_VER, true );
    }

    public function shortcode( $atts ) {
        wp_enqueue_style( 'pl-frontend' );
        wp_enqueue_script( 'pl-frontend' );

        $atts = shortcode_atts( array(
            'category' => '',
            'tag'      => '',
        ), $atts, 'prompts_library' );

        // Pull terms from main site
        $current = get_current_blog_id();
        $main    = function_exists('get_main_site_id') ? get_main_site_id() : 1;

        switch_to_blog( $main );

        $categories = get_terms( array( 'taxonomy' => 'prompt_category', 'hide_empty' => true ) );
        $tags       = get_terms( array( 'taxonomy' => 'prompt_tag',      'hide_empty' => true ) );

        restore_current_blog();

        // Normalize term collections
        $categories = ( is_wp_error( $categories ) || ! is_array( $categories ) ) ? array() : $categories;
        $tags       = ( is_wp_error( $tags )       || ! is_array( $tags ) )       ? array() : $tags;

        ob_start(); ?>
        <div class="pl-wrap">
            <div class="pl-filters">
                <select id="pl-filter-category">
                    <option value=""><?php esc_html_e('All Categories','prompts-library'); ?></option>
                    <?php foreach ( $categories as $cat ) :
                        $cat_slug = is_object( $cat ) ? ( $cat->slug ?? '' ) : ( ( is_array( $cat ) && isset( $cat['slug'] ) ) ? $cat['slug'] : '' );
                        $cat_name = is_object( $cat ) ? ( $cat->name ?? '' ) : ( ( is_array( $cat ) && isset( $cat['name'] ) ) ? $cat['name'] : '' );
                        if ( $cat_slug === '' && $cat_name === '' ) { continue; }
                    ?>
                        <option value="<?php echo esc_attr( $cat_slug ); ?>" <?php selected( $atts['category'], $cat_slug ); ?>>
                            <?php echo esc_html( $cat_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="pl-filter-tag">
                    <option value=""><?php esc_html_e('All Tags','prompts-library'); ?></option>
                    <?php foreach ( $tags as $tag_term ) :
                        $t_slug = is_object( $tag_term ) ? ( $tag_term->slug ?? '' ) : ( ( is_array( $tag_term ) && isset( $tag_term['slug'] ) ) ? $tag_term['slug'] : '' );
                        $t_name = is_object( $tag_term ) ? ( $tag_term->name ?? '' ) : ( ( is_array( $tag_term ) && isset( $tag_term['name'] ) ) ? $tag_term['name'] : '' );
                        if ( $t_slug === '' && $t_name === '' ) { continue; }
                    ?>
                        <option value="<?php echo esc_attr( $t_slug ); ?>" <?php selected( $atts['tag'], $t_slug ); ?>>
                            <?php echo esc_html( $t_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="pl-cards" class="pl-cards" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
                <!-- Cards will be loaded via AJAX -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
