<?php

namespace RunThroughHistory\WPGraphQL\Connection;

use GraphQL\Error\UserError;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Taxonomies\UserSexTaxonomy;
use RunThroughHistory\Taxonomies\UserAgeGroupTaxonomy;
use RunThroughHistory\WPGraphQL\Type\Enum\UserSexEnum;
use RunThroughHistory\WPGraphQL\Type\Enum\UserAgeGroupEnum;

class Users implements Hookable {
    public function register_hooks() {
        add_action( 'graphql_register_types',                    [ $this, 'register_where_input_fields' ] );
        add_filter( 'graphql_map_input_fields_to_wp_user_query', [ $this, 'modify_user_query_input_fields' ], 10, 6 );
    }

    public function register_where_input_fields() {
        register_graphql_fields( 'RootQueryToUserConnectionWhereArgs', [
            'sex' => [
                'type'        => UserSexEnum::TYPE,
                'description' => __( 'User\'s sex', 'run-through-music-history' ),
            ],
            'ageGroup' => [
                'type'        => UserAgeGroupEnum::TYPE,
                'description' => __( 'User\'s age group', 'run-through-music-history' ),
            ],
        ] );
    }

    public function modify_user_query_input_fields( array $query_args ) : array {
        // If no term slugs were provided, return the query args unchanged.
        if ( empty( $query_args['sex'] ) && empty( $query_args['ageGroup'] ) ) {
            return $query_args;
        }

        $include = $this->get_user_ids_to_include( $query_args );

        // If no users were found, ensure that no results come back by only including
        // a non-existent user ID of 0.
        if ( ! $include ) {
            $query_args['include'] = [ 0 ];
            return $query_args;
        }

        // If users were found, limit results to those users.
        $query_args['include'] = $include;
        return $query_args;
    }

    private function get_user_ids_to_include( array $query_args ) : array {
        $arrays_to_intersect = [];

        if ( ! empty( $query_args['include'] ) ) {
            $arrays_to_intersect[] = $query_args['include'];
        }

        if ( ! empty( $query_args['sex'] ) ) {
            $arrays_to_intersect[] = $this->get_users_with_term( $query_args['sex'], UserSexTaxonomy::KEY );
        }

        if ( ! empty( $query_args['ageGroup'] ) ) {
            $arrays_to_intersect[] = $this->get_users_with_term( $query_args['ageGroup'], UserAgeGroupTaxonomy::KEY );
        }

        if ( count( $arrays_to_intersect ) > 1 ) {
            return array_intersect( ...$arrays_to_intersect );
        }

        return $arrays_to_intersect[0];
    }

    private function get_users_with_term( string $term_slug, string $taxonomy ) : array {
        $term = get_term_by( 'slug', $term_slug, $taxonomy );

        if ( ! $term ) {
            return [];
        }

        return array_map( 'absint', get_objects_in_term( $term->term_id, $taxonomy ) );
    }
}
