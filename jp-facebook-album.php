<?php
/**
 * Plugin Name: JP Facebook Album
 * Plugin URI: https://github.com/jasonpau/jp-facebook-album/
 * Description: A simple plugin that adds a basic WP admin interface for authenticating with Facebook's Graph API
 * Version: 1.0.2
 * Author: Jason Pau
 * Author URI: https://jasonpau.io
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once 'inc/jp-facebook-album.php';

$jp_facebook_album = new JpFacebookAlbum();
$jp_facebook_album->bind_to_wordpress();
