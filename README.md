# JP Facebook Album

Creates a little admin panel within the WordPress dashboard for entering your Facebook app ID and secret, and subsequently handles the OAuth 2.0 tokens and stuff. Provides a shortcode for a masonry-style image gallery.

## Installation/Setup

Note: these instructions assume you already have a [Facebook App](https://developers.facebook.com/) set up.

### Environment

* [WordPress 4.9+](https://wordpress.org/download/)
* PHP 5.6+
* [Composer](https://getcomposer.org/)

### Installation Steps

1. Copy/clone this repo to the plugins folder of your WordPress website, e.g. `/website/wp-content/plugins/jp-facebook-album`

2. Run `composer install` to install the Facebook Graph API SDK dependency.

3. All done!

## Usage

### Authenticate with Facebook

1. Log in to your WordPress website, and navigate to **Settings > Facebook Album**.

2. Enter your Facebook App ID, App Secret, and the Album ID of the photo gallery you wish to display on your WordPress website, and click "Update".

3. Click "Authenticate App with Facebook". You'll be presented with a rather ugly page that says "All done!" at the bottom.

### Add Image Gallery to Website

1. Simply add the shortcode `[jp_facebook_album]` to any page on which you'd like to display your chosen Facebook image gallery!

## Troubleshooting

If you encounter an error while using this plugin please open a [GitHub Issue](https://github.com/jasonpau/jp-facebook-album/issues).
