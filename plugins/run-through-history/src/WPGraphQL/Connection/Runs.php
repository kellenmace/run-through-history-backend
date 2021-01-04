<?php

namespace RunThroughHistory\WPGraphQL\Connection;

use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use RunThroughHistory\Interfaces\Hookable;

class Runs implements Hookable {
    public function register_hooks() {
        add_filter( 'graphql_post_object_connection_query_args', [ $this, 'modify_query_args' ], 10, 5 );
    }

    /**
     * Filter the $query args to order runs by date descending.
     *
     * @param array       $query_args The args that will be passed to the WP_Query
     * @param mixed       $source     The source that's passed down the GraphQL queries
     * @param array       $args       The inputArgs on the field
     * @param AppContext  $context    The AppContext passed down the GraphQL tree
     * @param ResolveInfo $info       The ResolveInfo passed down the GraphQL tree
     */
    function modify_query_args( array $query_args, $source, array $args, AppContext $context, ResolveInfo $info ) : array {
        if ( 'runs' !== $info->fieldName ) {
            return $query_args;
        }

        // If orderby is already set, leave it as-is.
        if ( isset( $query_args['orderby'] ) ) {
            return $query_args;
        }

        // Order by the date the run occurred rather than the published date.
        $query_args['meta_key'] = 'date';
        $query_args['order']    = 'DESC';
        $query_args['orderby']  = 'meta_value_num';

        return $query_args;
    }
}
