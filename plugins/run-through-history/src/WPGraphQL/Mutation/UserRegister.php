<?php
namespace RunThroughHistory\WPGraphQL\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use WPGraphQL\AppContext;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\UserMetaSetters\AgeGroupSetter;
use RunThroughHistory\WPGraphQL\Type\Enum\StateEnum;
use RunThroughHistory\WPGraphQL\Type\Enum\UserSexEnum;
use RunThroughHistory\Taxonomies\UserSexTaxonomy;

class UserRegister implements Hookable {
    /**
     * AgeGroupSetter instance.
     *
     * @var AgeGroupSetter
     */
    private $age_group_setter;

    public function __construct( AgeGroupSetter $age_group_setter ) {
        $this->age_group_setter = $age_group_setter;
    }

    public function register_hooks() {
        add_action( 'graphql_register_types',                              [ $this, 'register_input_fields'] );
        add_action( 'graphql_before_resolve_field',                        [ $this, 'validate_fields' ], 10, 7 );
        add_action( 'graphql_user_object_mutation_update_additional_data', [ $this, 'save_additional_data' ], 10, 3 );
    }

    public function register_input_fields() {
        register_graphql_fields( 'RegisterUserInput', [
            'city' => [
                'type'        => [ 'non_null' => 'String' ],
                'description' => __( 'User\'s city', 'run-through-history' ),
            ],
            'state' => [
                'type'        => [ 'non_null' => StateEnum::TYPE ],
                'description' => __( 'User\'s state', 'run-through-history' ),
            ],
            'sex' => [
                'type'        => [ 'non_null' => UserSexEnum::TYPE ],
                'description' => __( 'User\'s sex', 'run-through-history' ),
            ],
            'dateOfBirth' => [
                'type'        => [ 'non_null' => 'String' ],
                'description' => __( 'User\'s date of birth in yyyy-mm-dd format', 'run-through-history' ),
            ],
        ] );
    }

    /**
     * Fire an action BEFORE the field resolves
     *
     * @param mixed           $source         Source passed down the Resolve Tree.
     * @param array           $args           Args for the field.
     * @param AppContext      $context        AppContext passed down the ResolveTree.
     * @param ResolveInfo     $info           ResolveInfo passed down the ResolveTree.
     * @param mixed           $field_resolver Field resolver.
     * @param string          $type_name      Name of the type the fields belong to.
     * @param string          $field_key      Name of the field.
     * @param FieldDefinition $field          Field Definition for the resolving field.
     */
    public function validate_fields( $source, array $args, AppContext $context, ResolveInfo $info, $field_resolver, string $type_name, string $field_key ) : void {
        if ( 'RootMutation' !== $type_name || ! $this->is_register_user_mutation( $field_key ) ) {
            return;
        }

        if ( ! $this->is_date_of_birth_valid( $args ) ) {
            throw new UserError( 'An invalid date of birth was provided.' );
        }
    }

    private function is_date_of_birth_valid( array $args ) : bool {
        return false !== strtotime( $args['input']['dateOfBirth'] );
    }

    /**
     * @param int    $user_id       The ID of the user being mutated.
     * @param array  $input         The input for the mutation.
     * @param string $mutation_name The name of the mutation (ex: create, update, delete).
     */
    public function save_additional_data( int $user_id, array $input, string $mutation_name ) : void {
        if ( ! $this->is_register_user_mutation( $mutation_name ) ) {
            return;
        }

        $dob_timestamp = strtotime( $input['dateOfBirth'] );
        $dob_string    = date( 'Y-m-d', $dob_timestamp );
        $dob_sanitized = sanitize_text_field( $dob_string );

        update_user_meta( $user_id, 'date_of_birth', $dob_sanitized );
        update_user_meta( $user_id, 'city', sanitize_text_field( $input['city'] ) );
        update_user_meta( $user_id, 'state', sanitize_text_field( $input['state'] ) );

        // Use date of birth data to set the user's age group.
        $this->age_group_setter->set( $user_id );

        wp_set_object_terms( $user_id, sanitize_text_field( $input['sex'] ), UserSexTaxonomy::KEY );
    }

    private function is_register_user_mutation( string $mutation_name ) : bool {
        return 'registerUser' === $mutation_name;
    }
}
