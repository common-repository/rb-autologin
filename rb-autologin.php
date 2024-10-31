<?php
/**
 * Plugin Name: (rb) AutoLogin
 * Version: 1.0
 * Description: Auto login (using AUTO_LOGIN_USERNAME constant in wp-config.php)
 * Plugin URI: https://
 * License: GPLv2 or later
 * Author: Ryan Briscall
 * GitHub Plugin URI: https://
 * Constants: AUTO_LOGIN_USERNAME
 */

defined('ABSPATH') or die('Direct script access not allowed.');

class RB_Autologin {

	private $is_initialized;

    public function __construct() { }

    public function register()
    {
        add_action( 'init', array( $this, 'rb_init' ) );
    }

	public function initialize() {
		if ( $this->is_initialized ) {
			return $this;
		}
		$this->is_initialized = true;
    }

    public function rb_init() {
        if ( is_user_logged_in() ) return;

        if ( ! is_admin() ) return;

        if ( ! defined( 'AUTO_LOGIN_USERNAME' ) ) return;

        // Abort on production (online).
        $auto_login_online = ( ! defined( 'AUTO_LOGIN_ONLINE' ) ) ? false : AUTO_LOGIN_ONLINE;
        if ( ! $auto_login_online && in_array( $_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'] ) ) return;

        add_filter( 'authenticate', array( $this, 'rb_auto_login'), 10, 3 );
        $user = wp_signon( array( 'user_login' => AUTO_LOGIN_USERNAME ) );
        remove_filter( 'authenticate', array( $this, 'rb_auto_login'), 10, 3 );

        if ( ! is_a( $user, 'WP_User' ) ) return false;

        // Access granted!
        wp_set_current_user( $user->ID, $user->user_login );
        if ( is_user_logged_in() )
        {
            $redirect_to = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
            wp_safe_redirect( $redirect_to );
            exit;
        }
    }

	public function rb_activate() {
        add_action( 'activated_plugin', array( $this, 'rb_usage_warning' ), 10, 2 );
    }

    public function rb_auto_login( $user, $username, $password ) {
        return get_user_by( 'login', $username );
    }

    public function rb_usage_warning() {
        $msg = '';
        $msg .= '<style>code { padding: 3px 5px 2px 5px; margin: 0 1px; background: #eaeaea; background: rgba(0,0,0,.07); font-size: 13px; font-family: Consolas,Monaco,monospace; }</style>';
        $msg .= '<div class="updated">';
        $msg .= '<h4>' . __( '(rb) AutoLogin' ) . '</h4>';
        $msg .= '<p>' . sprintf( __( "Make sure to add %s to your %s" ), "<code>define('AUTO_LOGIN_USERNAME', 'admin');</code>", "<code>wp-config.php</code>" ) . '</h4>';
        $msg .= '<p>' . sprintf( __( "If you're using a different username, then change %s to %s." ), "<code>'admin'</code>", "<code>'yourname'</code>" ) . '</p>';
        $msg .= '<p>' . __( "Important: Do not use this plug-in on your live (production) website, for security reasons." ) . '</p>';
        $msg .= '<p>' . sprintf( __( "This plug-in will attempt to prevent live (production) usage by performing a local check by looking for %s and %s.  If you want to bypass this, then add %s to your %s <strong>(not recommended)</strong>"), "<code>127.0.0.1</code>", "<code>::1</code>", "<code>define('AUTO_LOGIN_ONLINE', true);</code>", "<code>wp-config.php</code>" ) . '</p>';
        $msg .= '</div>';
        $msg .= sprintf( '<p style="text-align:center"><button class="button button-large" onclick="location.href=\'%s\'">%s</button></p>', esc_url( get_admin_url( null, 'plugins.php' ) ), __( 'Okay' ) );

        wp_die( $msg );
    }

}

if ( class_exists( 'RB_Autologin' ) ) {
    $rb_autologin = new RB_Autologin();
    $rb_autologin->register();

    register_activation_hook( __FILE__, array( $rb_autologin, 'rb_activate' ) );
}
