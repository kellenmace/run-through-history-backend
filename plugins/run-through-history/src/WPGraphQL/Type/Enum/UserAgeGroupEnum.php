<?php

namespace RunThroughHistory\WPGraphQL\Type\Enum;

use WPGraphQL\Type\WPEnumType;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\Enum;
use RunThroughHistory\Taxonomies\UserAgeGroupTaxonomy;

class UserAgeGroupEnum implements Hookable, Enum {
    const TYPE = 'UserAgeGroupEnum';

    public function register_hooks() {
        add_action( 'graphql_register_types', [ $this, 'register' ] );
    }

    public function register() {
        register_graphql_enum_type(
            self::TYPE,
            [
                'description' => __( 'User age group.', 'run-through-history' ),
                'values'      => [
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_0_10 ) => [
                        'description' => __( 'Under 10', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_0_10,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_10_20 ) => [
                        'description' => __( '10 to 20', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_10_20,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_20_30 ) => [
                        'description' => __( '20 to 30', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_20_30,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_30_40 ) => [
                        'description' => __( '30 to 40', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_30_40,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_40_50 ) => [
                        'description' => __( '40 to 50', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_40_50,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_50_60 ) => [
                        'description' => __( '50 to 60', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_50_60,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_60_70 ) => [
                        'description' => __( '60 to 70', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_60_70,
                    ],
                    WPEnumType::get_safe_name( UserAgeGroupTaxonomy::TERM_70_120 ) => [
                        'description' => __( '70 or better', 'run-through-history' ),
                        'value'       => UserAgeGroupTaxonomy::TERM_70_120,
                    ],
                ],
            ]
        );
    }
}
