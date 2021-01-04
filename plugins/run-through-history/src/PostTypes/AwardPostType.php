<?php

namespace RunThroughHistory\PostTypes;

use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\CustomPostType;

class AwardPostType implements Hookable, CustomPostType {
    use PostTypeLabelUtility;

    const KEY = 'award';
    const GRAPHQL_SINGLE_NAME = 'Award';

    public function register_hooks() {
        add_action( 'init', [ $this, 'register' ] );
    }

    public function register() {
        register_post_type( self::KEY, [
			'labels'              => $this->generate_labels( 'Award', 'Awards' ),
            'public'              => true,
            'menu_icon'           => 'dashicons-awards',
            'supports'            => ['title', 'editor', 'thumbnail'],
            'show_in_graphql'     => true,
            'graphql_single_name' => self::GRAPHQL_SINGLE_NAME,
            'graphql_plural_name' => 'Awards',
		] );
    }
}
