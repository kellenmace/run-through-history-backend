<?php

namespace RunThroughHistory\Media;

use RunThroughHistory\Interfaces\Hookable;

class FeaturedImages implements Hookable {
    public function register_hooks() {
        add_action( 'after_setup_theme', [ $this, 'add_featured_image_support'] );
    }

    public function add_featured_image_support() {
        add_theme_support( 'post-thumbnails' );
    }
}
