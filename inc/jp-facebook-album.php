<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class JpFacebookAlbum {

    const CALLBACK_URL = 'https://jasonpau.io/blog/wp-admin/options-general.php?page=facebook-album';
    const DEFAULT_GRAPH_VERSION = 'v2.10';

    public function __construct() {

        // We use the session to hold the 'FBRLH_state' key, and it gets
        // compared upon return to the callback page to prevent CSRF attacks.
        if ( !session_id() ) {
            session_start();
        }

        $this->code = isset( $_GET['code'] ) ? $_GET['code'] : null;

        if ( isset( $_POST['app_id'] ) ) {
            update_option( 'jp_facebook_album_app_id', sanitize_text_field( $_POST['app_id'] ) );
        }

        if ( isset( $_POST['app_secret'] ) ) {
            update_option( 'jp_facebook_album_app_secret', sanitize_text_field( $_POST['app_secret'] ) );
        }

        if ( isset( $_POST['album_id'] ) ) {
            update_option( 'jp_facebook_album_album_id', sanitize_text_field( $_POST['album_id'] ) );
        }

        $this->app_id = get_option( 'jp_facebook_album_app_id', null );
        $this->app_secret = get_option( 'jp_facebook_album_app_secret', null );
        $this->album_id = get_option( 'jp_facebook_album_album_id', null );
    }

    // Here's where we start hooking everythin into WordPress
    public function bind_to_wordpress() {
        add_action( 'admin_menu', array( $this, 'admin_page' ), 100 );
    }

    /**
     * Add our admin panel to the WP Settings
     */
    public function admin_page() {
        $auth_page = add_options_page(
            'Facecbook Album',
            'Facecbook Album',
            'manage_options',
            'facebook-album',
            array( $this, 'controller' )
        );
    }

    public function controller() {
        // If we're coming here from the callback...
        if ( isset( $this->code ) ) {
            return $this->callback_page();

        // Otherwise we're coming via a typical page load.
        } else {
            return $this->admin_settings_panel_setup();
        }
    }

    // This generates the page we see after authorizing the app on Facebook.
    public function callback_page() {
        require_once __DIR__ . '/../views/callback.php';
        echo '<br />-----sessionn var dump-----<br />';
        var_dump($_SESSION);
    }

    // This generates the normal admin settings page.
    public function admin_settings_panel_setup() {
        if ( $this->app_id && $this->app_secret ) {
            $login_url = $this->get_authorize_login_url();
        }

        require_once __DIR__ . '/../views/settings.php';
        echo '<br />-----sessionn var dump-----<br />';
        var_dump($_SESSION);
    }


    public function get_access_token() {

    }

    /**
     * Private Methods
     */

    private function get_authorize_login_url() {

        // Session is necessary for initial authorization, as we leave
        // the site for Facebook, then come back via redirect URI
        if ( ! session_id() ) {
            session_start();
        }

        $fb = new Facebook\Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => self::DEFAULT_GRAPH_VERSION,
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions TODO possibly remove?
        $loginUrl = $helper->getLoginUrl( self::CALLBACK_URL, $permissions );

        return $loginUrl;
    }


}