<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

$photos = $album['photos']['data'];

$grid_items = '';

if ( is_array( $photos ) ) {
    foreach ( $photos as $photo ) {
        $grid_item_f = <<<HTML
            <div class="grid-item">
                <a href="%s" target="_blank" rel="noopener">
                    <img src="%s" alt="" />
                </a>
            </div>
HTML;

        $grid_items .= sprintf( $grid_item_f,
                                $photo['link'],
                                end($photo['images'])['source'] );
    }
}

$gallery_f = <<<HTML
<div class="jp-facebook-grid">
    <div class="grid-sizer"></div>
    %s
<div>
HTML;

printf( $gallery_f, $grid_items );