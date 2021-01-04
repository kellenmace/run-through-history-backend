<?php

namespace RunThroughHistory\WPGraphQL\Type\Enum;

use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\Enum;
use RunThroughHistory\Utilities\States;

class StateEnum implements Hookable, Enum {
    use States;

    const TYPE = 'StateEnum';

    public function register_hooks() {
        add_action( 'graphql_register_types', [ $this, 'register' ] );
    }

    public function register() {
        register_graphql_enum_type(
            self::TYPE,
            [
                'description' => __( 'User\'s state.', 'run-through-history' ),
                'values'      => $this->get_state_fields(),
            ]
        );
    }

    private function get_state_fields() : array {
        return array_reduce( $this->get_states(), function( array $fields, string $state ) : array {
            $fields[ $state ] = [
                'description' => $state,
                'value'       => $state,
            ];

            return $fields;
        }, [] );
    }
}
