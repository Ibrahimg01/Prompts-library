<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PL_Settings {

    public function __construct() {
        if ( is_network_admin() ) {
            add_action( 'network_admin_menu', array( $this, 'register_page' ) );
        }
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_network_options' ) ) return;

        if ( isset( $_POST['pl_settings_nonce'] ) && wp_verify_nonce( $_POST['pl_settings_nonce'], 'pl_save_settings' ) ) {
            // Example: per-page count
            $per_page = isset($_POST['pl_per_page']) ? max(1, (int) $_POST['pl_per_page']) : 9;
            update_site_option( 'pl_per_page', $per_page );
            echo '<div class="updated"><p>'.esc_html__('Saved.','prompts-library').'</p></div>';
        }

        $per_page = (int) get_site_option( 'pl_per_page', 9 );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Prompts Library Settings','prompts-library'); ?></h1>
            <form method="post">
                <?php wp_nonce_field( 'pl_save_settings', 'pl_settings_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="pl_per_page"><?php esc_html_e('Prompts Per Page','prompts-library'); ?></label></th>
                        <td><input type="number" min="1" id="pl_per_page" name="pl_per_page" value="<?php echo esc_attr( $per_page ); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function register_page() {
        add_submenu_page(
            'prompts-library',
            __( 'Settings', 'prompts-library' ),
            __( 'Settings', 'prompts-library' ),
            'manage_network_options',
            'prompts-library-settings',
            array( __CLASS__, 'render_page' )
        );
    }
}
