<?php

namespace RunThroughHistory\WPGraphQL\Type\Object;

use WPGraphQL\Model\User as UserModel;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Taxonomies\UserSexTaxonomy;
use RunThroughHistory\Taxonomies\UserAgeGroupTaxonomy;

class User implements Hookable {
    public function register_hooks() {
        add_action( 'graphql_register_types', [ $this, 'register_fields' ] );
    }

    public function register_fields() {
        register_graphql_fields( 'User', [
            'sex' => [
                'type'        => 'String',
				'description' => __( 'User\'s sex', 'run-through-history' ),
				'resolve'     => function( UserModel $user ) {
                    $data = $user->data;
                    $terms = wp_get_object_terms( $user->fields['userId'], UserSexTaxonomy::KEY );

                    return $terms ? $terms[0]->slug : null;
				},
            ],
            'ageGroup' => [
                'type'        => 'String',
				'description' => __( 'User\'s age group', 'run-through-history' ),
				'resolve'     => function( UserModel $user ) {
                    $terms = wp_get_object_terms( $user->fields['userId'], UserAgeGroupTaxonomy::KEY );

                    return $terms ? $terms[0]->slug : null;
				},
            ],
        ] );
    }
}
