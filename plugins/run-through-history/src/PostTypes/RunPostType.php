<?php

namespace RunThroughHistory\PostTypes;

use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\CustomPostType;

class RunPostType implements Hookable, CustomPostType {
    use PostTypeLabelUtility;

    const KEY = 'run';
    const GRAPHQL_SINGLE_NAME = 'Run';

    public function register_hooks() {
        add_action( 'init', [ $this, 'register' ] );
    }

    public function register() {
        register_post_type( self::KEY, [
			'labels'              => $this->generate_labels( 'Run', 'Runs' ),
            'public'              => true,
            'menu_icon'           => 'dashicons-location-alt',
            'supports'            => ['title', 'author'],
            'show_in_graphql'     => true,
            'graphql_single_name' => self::GRAPHQL_SINGLE_NAME,
            'graphql_plural_name' => 'Runs',
		] );
    }
}
