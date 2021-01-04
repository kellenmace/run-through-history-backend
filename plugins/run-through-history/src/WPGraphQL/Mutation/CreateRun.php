<?php

namespace RunThroughHistory\WPGraphQL\Mutation;

use WP_Post_Type;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\PostTypes\RunPostType;

class CreateRun implements Hookable {
    public function register_hooks() {
        add_action( 'graphql_register_types',                              [ $this, 'register_input_fields'] );
        add_action( 'graphql_before_resolve_field',                        [ $this, 'validate' ], 10, 7 );
        add_action( 'graphql_post_object_mutation_update_additional_data', [ $this, 'save_additional_data' ], 10, 4 );
    }

    public function register_input_fields() {    
        $input_type = 'Create' . RunPostType::GRAPHQL_SINGLE_NAME . 'Input';

        register_graphql_fields( $input_type, [
            'runDate' => [
                'type'        => [ 'non_null' => 'String' ],
                'description' => __( 'Date of the run in yyyy-mm-dd format', 'run-through-music-history' ),
            ],
            'miles' => [
                'type'        => [ 'non_null' => 'Float' ],
                'description' => __( 'Miles that were run', 'run-through-music-history' ),
            ],
        ] );
    }

    /**
     * @param mixed           $source         Source passed down the Resolve Tree.
     * @param array           $args           Args for the field.
     * @param AppContext      $context        AppContext passed down the ResolveTree.
     * @param ResolveInfo     $info           ResolveInfo passed down the ResolveTree.
     * @param mixed           $field_resolver Field resolver.
     * @param string          $type_name      Name of the type the fields belong to.
     * @param string          $field_key      Name of the field.
     * @param FieldDefinition $field          Field Definition for the resolving field.
     */
    public function validate( $source, array $args, AppContext $context, ResolveInfo $info, $field_resolver, string $type_name, string $field_key ) : void {
        // Make sure this is the createRun field on the RootMutation.
        if ( 'RootMutation' !== $type_name || 'create' . RunPostType::GRAPHQL_SINGLE_NAME !== $field_key ) {
            return;
        }

        if ( ! $this->is_run_date_valid( $args ) ) {
            throw new UserError( 'Run date is invalid.' );
        }
    }

    private function is_run_date_valid( array $args ) : bool {
        return false !== strtotime( $args['input']['runDate'] );
    }

    public function save_additional_data( int $post_id, array $input, WP_Post_Type $post_type_object, string $mutation_name ) : void {
        // Make sure this is the createRun mutation.
        if ( 'create' . RunPostType::GRAPHQL_SINGLE_NAME !== $mutation_name ) {
            return;
        }

        $date_sanitized = sanitize_text_field( $input['runDate'] );
        $date_timestamp = strtotime( $date_sanitized );
        $meta_date      = date( 'Ymd', $date_timestamp );
        $title_date     = date( 'm/d/Y', $date_timestamp );
        $miles          = sanitize_text_field( $input['miles'] );
        $user_id        = get_post_field( 'post_author', $post_id );
        $users          = get_users( [ 'include' => $user_id, 'fields' => [ 'display_name' ] ] );
        $name           = $users[0]->display_name ?? '';

        update_post_meta( $post_id, 'date', $meta_date );
        update_post_meta( $post_id, 'miles', $miles );

        wp_update_post( [
            'ID'         => $post_id,
            'post_title' => "{$name} - {$title_date}",
        ] );
    }
}
