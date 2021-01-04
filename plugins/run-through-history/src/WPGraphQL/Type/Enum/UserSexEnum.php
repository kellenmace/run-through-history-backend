<?php

namespace RunThroughHistory\WPGraphQL\Type\Enum;

use WPGraphQL\Type\WPEnumType;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\Enum;
use RunThroughHistory\Taxonomies\UserSexTaxonomy;

class UserSexEnum implements Hookable, Enum {
    const TYPE = 'UserSexEnum';

    public function register_hooks() {
        add_action( 'graphql_register_types', [ $this, 'register' ] );
    }

    public function register() {
        register_graphql_enum_type(
            self::TYPE,
            [
                'description' => __( 'User sex.', 'run-through-history' ),
                'values'      => [
                    WPEnumType::get_safe_name( UserSexTaxonomy::FEMALE_TERM ) => [
                        'description' => __( 'Female', 'run-through-history' ),
                        'value'       => UserSexTaxonomy::FEMALE_TERM,
                    ],
                    WPEnumType::get_safe_name( UserSexTaxonomy::MALE_TERM ) => [
                        'description' => __( 'Male', 'run-through-history' ),
                        'value'       => UserSexTaxonomy::MALE_TERM,
                    ],
                ],
            ]
        );
    }
}
