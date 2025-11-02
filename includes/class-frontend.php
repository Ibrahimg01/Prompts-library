<?php
/**
 * Frontend Handler
 *
 * @package Prompts_Library
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Frontend Class
 */
class Prompts_Library_Frontend {

    /**
     * Single instance
     *
     * @var Prompts_Library_Frontend
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Prompts_Library_Frontend
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
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_prompts-library-view' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'prompts-library-frontend',
            PROMPTS_LIBRARY_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            PROMPTS_LIBRARY_VERSION
        );

        wp_enqueue_script(
            'prompts-library-frontend',
            PROMPTS_LIBRARY_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            PROMPTS_LIBRARY_VERSION,
            true
        );

        wp_localize_script(
            'prompts-library-frontend',
            'promptsLibrary',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'prompts_library_nonce' ),
            )
        );
    }

    /**
     * Render library page
     */
    public static function render_library_page() {
        $current_site_id = get_current_blog_id();
        
        // Get settings
        $settings = get_site_option( 'prompts_library_settings', array(
            'prompts_per_page' => 9,
            'cards_per_row' => 3,
        ) );

        // Get filter parameters
        $paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        $category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : '';
        $tag = isset( $_GET['tag'] ) ? sanitize_text_field( $_GET['tag'] ) : '';

        // Build query
        $args = array(
            'post_type'      => 'prompt',
            'post_status'    => 'publish',
            'posts_per_page' => $settings['prompts_per_page'],
            'paged'          => $paged,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        );

        if ( ! empty( $search ) ) {
            $args['s'] = $search;
        }

        $tax_query = array();
        if ( ! empty( $category ) ) {
            $tax_query[] = array(
                'taxonomy' => 'prompt_category',
                'field' => 'slug',
                'terms' => $category,
            );
        }

        if ( ! empty( $tag ) ) {
            $tax_query[] = array(
                'taxonomy' => 'prompt_tag',
                'field' => 'slug',
                'terms' => $tag,
            );
        }

        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        // Switch to main site to get prompts
        switch_to_blog( get_main_site_id() );

        $all_prompt_ids = get_posts(
            array(
                'post_type'   => 'prompt',
                'post_status' => 'publish',
                'numberposts' => -1,
                'meta_key'    => 'pl_publish_all',
                'meta_value'  => '1',
                'fields'      => 'ids',
            )
        );

        $targeted_prompt_ids = get_posts(
            array(
                'post_type'   => 'prompt',
                'post_status' => 'publish',
                'numberposts' => -1,
                'fields'      => 'ids',
                'meta_query'  => array(
                    array(
                        'key'     => 'pl_publish_sites',
                        'value'   => '"' . $current_site_id . '"',
                        'compare' => 'LIKE',
                    ),
                ),
            )
        );

        $legacy_prompt_ids = get_posts(
            array(
                'post_type'   => 'prompt',
                'post_status' => 'publish',
                'numberposts' => -1,
                'fields'      => 'ids',
                'meta_query'  => array(
                    array(
                        'key'     => '_published_sites',
                        'value'   => '"' . $current_site_id . '"',
                        'compare' => 'LIKE',
                    ),
                ),
            )
        );

        $all_prompt_ids      = array_map( 'intval', $all_prompt_ids );
        $targeted_prompt_ids = array_map( 'intval', $targeted_prompt_ids );
        $legacy_prompt_ids   = array_map( 'intval', $legacy_prompt_ids );

        if ( ! empty( $all_prompt_ids ) && ! empty( $targeted_prompt_ids ) ) {
            $targeted_prompt_ids = array_diff( $targeted_prompt_ids, $all_prompt_ids );
        }

        $allowed_ids = array_values(
            array_unique(
                array_merge( $all_prompt_ids, $targeted_prompt_ids, $legacy_prompt_ids )
            )
        );

        if ( empty( $allowed_ids ) ) {
            $allowed_ids = array( 0 );
        }

        $args['post__in'] = $allowed_ids;

        $query = new WP_Query( $args );
        $categories = get_terms( array( 'taxonomy' => 'prompt_category', 'hide_empty' => true ) );
        $tags = get_terms( array( 'taxonomy' => 'prompt_tag', 'hide_empty' => true ) );
        restore_current_blog();

        if ( is_wp_error( $categories ) || ! is_array( $categories ) ) {
            $categories = array();
        } else {
            $categories = array_values(
                array_filter(
                    $categories,
                    function ( $term ) {
                        return $term instanceof WP_Term;
                    }
                )
            );
        }

        if ( is_wp_error( $tags ) || ! is_array( $tags ) ) {
            $tags = array();
        } else {
            $tags = array_values(
                array_filter(
                    $tags,
                    function ( $term ) {
                        return $term instanceof WP_Term;
                    }
                )
            );
        }

        ?>
        <div class="wrap prompts-library-wrap">
            <!-- Header Container -->
            <div class="prompts-library-header">
                <div class="header-content">
                    <h1 class="library-title">Premium Prompts Library</h1>
                    <p class="library-subtitle">A Database of Curated, Optimized, & Battle-Tested Power Prompts</p>
                    
                    <!-- Embedded Chatbot -->
                    <div class="chatbot-container">
                        <?php echo do_shortcode( '[mwai_chatbot id="chatbot-njj2fe"]' ); ?>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="prompts-library-filters">
                <form method="get" action="" class="filters-form">
                    <input type="hidden" name="page" value="prompts-library-view" />
                    
                    <div class="search-bar-container">
                        <input 
                            type="text" 
                            name="s" 
                            class="prompts-search-input" 
                            placeholder="Search prompts..." 
                            value="<?php echo esc_attr( $search ); ?>"
                        />
                        <button type="submit" class="search-button">
                            <span class="dashicons dashicons-search"></span>
                        </button>
                    </div>

                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="category-filter"><?php esc_html_e( 'Category:', 'prompts-library' ); ?></label>
                            <select name="category" id="category-filter" class="filter-select">
                                <option value=""><?php esc_html_e( 'All Categories', 'prompts-library' ); ?></option>
                                <?php foreach ( $categories as $cat ) : ?>
                                    <option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $category, $cat->slug ); ?>>
                                        <?php echo esc_html( $cat->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="tag-filter"><?php esc_html_e( 'Tag:', 'prompts-library' ); ?></label>
                            <select name="tag" id="tag-filter" class="filter-select">
                                <option value=""><?php esc_html_e( 'All Tags', 'prompts-library' ); ?></option>
                                <?php foreach ( $tags as $tag_term ) : ?>
                                    <option value="<?php echo esc_attr( $tag_term->slug ); ?>" <?php selected( $tag, $tag_term->slug ); ?>>
                                        <?php echo esc_html( $tag_term->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="filter-button"><?php esc_html_e( 'Apply Filters', 'prompts-library' ); ?></button>
                        
                        <?php if ( ! empty( $search ) || ! empty( $category ) || ! empty( $tag ) ) : ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=prompts-library-view' ) ); ?>" class="clear-filters">
                                <?php esc_html_e( 'Clear Filters', 'prompts-library' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Prompts Grid -->
            <div class="prompts-library-grid" data-cards-per-row="<?php echo esc_attr( $settings['cards_per_row'] ); ?>">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <?php
                        $prompt_id         = get_the_ID();
                        $raw_description   = get_post_meta( $prompt_id, 'pl_short_description', true );
                        if ( '' === $raw_description ) {
                            $raw_description = get_post_meta( $prompt_id, '_prompt_description', true );
                        }
                        $description       = trim( (string) $raw_description );
                        $prompt_text_value = get_post_meta( $prompt_id, 'pl_prompt_text', true );
                        if ( '' === $prompt_text_value ) {
                            $prompt_text_value = get_post_meta( $prompt_id, '_prompt_text', true );
                        }
                        $prompt_text      = (string) $prompt_text_value;
                        $prompt_categories = get_the_terms( $prompt_id, 'prompt_category' );
                        $primary_category  = ( ! is_wp_error( $prompt_categories ) && ! empty( $prompt_categories ) ) ? array_shift( $prompt_categories ) : null;
                        $prompt_tags       = get_the_terms( $prompt_id, 'prompt_tag' );

                        $cat_name = $primary_category ? $primary_category->name : '';
                        $cat_slug = $primary_category ? $primary_category->slug : '';
                        $cat_color = '';

                        if ( $primary_category instanceof WP_Term ) {
                            $stored_color = get_term_meta( $primary_category->term_id, 'category_color', true );
                            if ( ! empty( $stored_color ) ) {
                                $cat_color = sanitize_hex_color( $stored_color );
                            }
                        }
                        ?>
                        <div class="pl-card" data-prompt-id="<?php echo esc_attr( $prompt_id ); ?>" data-prompt="<?php echo esc_attr( $prompt_text ); ?>">
                            <div class="pl-card-body">
                                <?php if ( $cat_name ) : ?>
                                    <?php
                                    $badge_classes = 'pl-badge pl-badge--cat';
                                    if ( $cat_slug ) {
                                        $badge_classes .= ' pl-badge--' . sanitize_html_class( $cat_slug );
                                    }
                                    ?>
                                    <span class="<?php echo esc_attr( $badge_classes ); ?>"<?php echo $cat_color ? ' style="background-color: ' . esc_attr( $cat_color ) . ';"' : ''; ?>>
                                        <?php echo esc_html( $cat_name ); ?>
                                    </span>
                                <?php endif; ?>

                                <h3 class="pl-card-title"><?php the_title(); ?></h3>

                                <?php if ( $description ) : ?>
                                    <p class="pl-card-desc"><?php echo esc_html( $description ); ?></p>
                                <?php endif; ?>

                                <?php if ( $prompt_tags && ! is_wp_error( $prompt_tags ) ) : ?>
                                    <div class="pl-tags">
                                        <?php foreach ( $prompt_tags as $tag_item ) : ?>
                                            <span class="pl-tag"><?php echo esc_html( $tag_item->name ); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="pl-card-actions">
                                    <button type="button" class="pl-btn pl-btn--ghost view-prompt" data-prompt-id="<?php echo esc_attr( $prompt_id ); ?>">
                                        <?php esc_html_e( 'View Prompt', 'prompts-library' ); ?>
                                    </button>
                                    <button type="button" class="pl-btn pl-btn--primary use-prompt pl-use-prompt" data-prompt-id="<?php echo esc_attr( $prompt_id ); ?>" data-prompt="<?php echo esc_attr( $prompt_text ); ?>">
                                        <?php esc_html_e( 'Use Prompt', 'prompts-library' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="no-prompts">
                        <p><?php esc_html_e( 'No prompts found. Try adjusting your filters.', 'prompts-library' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ( $query->max_num_pages > 1 ) : ?>
                <div class="prompts-pagination">
                    <?php
                    $current_page = max( 1, $paged );
                    $total_pages = $query->max_num_pages;
                    
                    $base_url = add_query_arg( array(
                        'page' => 'prompts-library-view',
                        's' => $search,
                        'category' => $category,
                        'tag' => $tag,
                    ), admin_url( 'admin.php' ) );

                    // Previous button
                    if ( $current_page > 1 ) {
                        $prev_url = add_query_arg( 'paged', $current_page - 1, $base_url );
                        echo '<a href="' . esc_url( $prev_url ) . '" class="page-link prev-link">' . esc_html__( 'Previous', 'prompts-library' ) . '</a>';
                    }

                    // Page numbers
                    for ( $i = 1; $i <= $total_pages; $i++ ) {
                        if ( $i === $current_page ) {
                            echo '<span class="page-link current-page">' . esc_html( $i ) . '</span>';
                        } else {
                            $page_url = add_query_arg( 'paged', $i, $base_url );
                            echo '<a href="' . esc_url( $page_url ) . '" class="page-link">' . esc_html( $i ) . '</a>';
                        }
                    }

                    // Next button
                    if ( $current_page < $total_pages ) {
                        $next_url = add_query_arg( 'paged', $current_page + 1, $base_url );
                        echo '<a href="' . esc_url( $next_url ) . '" class="page-link next-link">' . esc_html__( 'Next', 'prompts-library' ) . '</a>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>

        <!-- Modal -->
        <div id="prompt-modal" class="prompt-modal" style="display: none;">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-category-badge"></span>
                    <button class="modal-close">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 class="modal-title"></h2>
                    <p class="modal-description"></p>
                    <div class="prompt-container">
                        <h4 class="prompt-label"><?php esc_html_e( 'Prompt', 'prompts-library' ); ?></h4>
                        <div class="prompt-text"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary copy-prompt">
                        <span class="dashicons dashicons-clipboard"></span>
                        <?php esc_html_e( 'Copy Prompt', 'prompts-library' ); ?>
                    </button>
                    <button class="btn btn-primary use-prompt-modal">
                        <span class="dashicons dashicons-controls-play"></span>
                        <?php esc_html_e( 'Use Prompt', 'prompts-library' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}
