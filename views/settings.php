<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Primary view with the fields for adding config values
 */
$config_f = <<<HTML
    <h1>Facebook Album Settings</h1>
    <p>This plugin is designed for developer use only.</p>
    <hr />
    <h3>Facebook App Config</h3>
    <form action="" method="POST">
        <p>
            <label for="app_id"><strong>App ID: %s</strong></label><br />
            <input name="app_id" />
        </p>
        <p>
            <label for="app_secret"><strong>App Secret: %s</strong></label><br />
            <input name="app_secret" />
        </p>
        <p>
            <label for="album_id"><strong>Album ID: %s</strong></label><br />
            <input name="album_id" />
        </p>
        <input type="submit" value="Update" />
    </form>
HTML;

$output = sprintf( $config_f, 
                    esc_html( $this->app_id ),
                    esc_html( $this->app_secret ),
                    esc_html( $this->album_id ) );

/**
 * Authentication-related view with the Facebook login link
 */
if ( isset( $login_url ) ) {
    $auth_f = <<<HTML
        <hr />
        <h3>Authentication</h3>
        <p><strong>Long-lived Access Token:</strong> %s</p>
        <p><a href="%s">Authenticate App with Facebook</a></p>
        <hr />
        <h3>Cache</h3>
        <pre>%s</pre>
HTML;

    $output .= sprintf( $auth_f,
                        $this->access_token,
                        esc_html( $login_url ),
                        json_encode( get_transient( 'jp_facebook_album_image_data' ) ) );
}

print( $output );
