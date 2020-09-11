<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class JpFacebookAlbum {

    const GRAPH_BASE_URL = 'https://graph.facebook.com';
    const DEFAULT_GRAPH_VERSION = 'v3.2';
    const NUMBER_OF_IMAGES = 9;

    public function __construct() {

        // We use the session to hold the 'FBRLH_state' key, and it gets
        // compared upon return to the callback page to prevent CSRF attacks.
        if ( !session_id() ) {
            session_start( array( 'read_and_close' => true ) );
        }

        $this->CALLBACK_URL = get_site_url() . '/wp-admin/options-general.php?page=facebook-album';
        $this->code = isset( $_GET['code'] ) ? $_GET['code'] : null;

        if ( ! empty( $_POST['app_id'] ) ) {
            update_option( 'jp_facebook_album_app_id', sanitize_text_field( $_POST['app_id'] ) );
        }

        if ( ! empty( $_POST['app_secret'] ) ) {
            update_option( 'jp_facebook_album_app_secret', sanitize_text_field( $_POST['app_secret'] ) );
        }

        if ( ! empty( $_POST['album_id'] ) ) {
            update_option( 'jp_facebook_album_album_id', sanitize_text_field( $_POST['album_id'] ) );
        }

        $this->app_id = get_option( 'jp_facebook_album_app_id', null );
        $this->app_secret = get_option( 'jp_facebook_album_app_secret', null );
        $this->album_id = get_option( 'jp_facebook_album_album_id', null );

        $this->access_token = get_option( 'jp_facebook_album_long_lived_access_token', null );
        $this->access_token_expiration = get_option( 'jp_facebook_album_long_lived_access_token_expiration', null );
    }

    // Here's where we start hooking everythin into WordPress
    public function bind_to_wordpress() {
        add_filter( 'widget_text', 'do_shortcode' );
        add_action( 'admin_menu', array( $this, 'admin_page' ), 100 );
        add_shortcode( 'jp_facebook_album', array( $this, 'localize_album_json' ) );
    }

    /**
     * Add our admin panel to the WP Settings
     */
    public function admin_page() {
        $auth_page = add_options_page(
            'Facebook Album',
            'Facebook Album',
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
        // TODO this is a huge mix of logic and view that needs refactoring.
        require_once __DIR__ . '/../views/callback.php';
    }

    // This generates the normal admin settings page.
    public function admin_settings_panel_setup() {
        if ( $this->app_id && $this->app_secret ) {
            $login_url = $this->get_authorize_login_url();
        }
        require_once __DIR__ . '/../views/settings.php';
    }

    public static function localize_album_json() {
        $jp_facebook_album = new JpFacebookAlbum();
        $album = $jp_facebook_album->get_album();

        wp_enqueue_style( 'jp-facebook-album-styles', plugins_url() . '/jp-facebook-album/css/styles.css', array(), '1.0.5' );
        wp_enqueue_script( 'jp-facebook-album-script', plugins_url() . '/jp-facebook-album/js/main.js', array( 'jquery', 'underscore' ), '1.1.0', true );
        wp_localize_script( 'jp-facebook-album-script', 'facebook_album_photos', $album );

        ob_start();
        require_once __DIR__ . '/../views/gallery.php';
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Private Methods
     */

    private function get_album() {
        $cached_album = get_transient( 'jp_facebook_album_image_data' );

        // Do we still have a valid cached album?
        if ( $cached_album ) {
            $album = $cached_album;
        } else {
            $album = $this->get_album_from_api();
        }

        return $album;
    }

    private function get_album_from_api() {
        if ( ! $this->album_id || ! $this->access_token ) {
            return null;
        }

        $args = array(
            'timeout' => 10,
            'headers'     => array(
                'content-type' => 'application/json',
            ),
            'method' => 'GET',
        );

        $url_f = '%s/%s/%s?fields=photos.limit(%s){images,link}&access_token=%s';

        $url = sprintf(
            $url_f,
            self::GRAPH_BASE_URL,
            self::DEFAULT_GRAPH_VERSION,
            $this->album_id,
            self::NUMBER_OF_IMAGES,
            $this->access_token
        );

        $response = wp_remote_request( $url, $args );

        // Make sure it's not a WP error object.
        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );

        $album = json_decode( $body, true );

        // Remove the paging properties as it contains an access token.
        unset( $album['photos']['paging'] );

        // Cached the data for one hour.
        set_transient( 'jp_facebook_album_image_data', $album, 60*60*1 );

        // Return the data too so we don't have to make two DB calls.
        return $album;
    }

    private function get_authorize_login_url() {
        // Session is necessary for initial authorization, as we leave
        // the site for Facebook, then come back via redirect URI
        if ( ! session_id() ) {
            session_start( array( 'read_and_close' => true ) );
        }

        $fb = new Facebook\Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => self::DEFAULT_GRAPH_VERSION,
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions TODO possibly remove?
        $loginUrl = $helper->getLoginUrl( $this->CALLBACK_URL, $permissions );

        return $loginUrl;
    }
}
