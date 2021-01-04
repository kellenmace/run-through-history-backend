<?php

namespace RunThroughHistory\UserRoles;

use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\UserRole;

class RolesRegistrar implements Hookable {
    /**
     * Custom roles version number. Bump this number if you have made any
     * permissions changes and need the roles to be re-registered.
     */
    const VERSION = 1;

    /**
     * UserRole instances.
     *
     * @var array
     */
    private $roles;

    public function __construct( array $instances ) {
        $this->roles = array_filter( $instances, function( $instance ) {
            return $instance instanceof UserRole;
        } );
    }

    public function register_hooks() {
        add_action( 'init', [ $this, 'maybe_register_roles' ] );
    }

    public function maybe_register_roles() {
        if ( $this->should_roles_be_set() ) {
            $this->remove_roles();
            $this->register_roles();
            update_option( 'rth_custom_roles_version', self::VERSION );
        }
    }

    /**
     * If the roles version const in this file is newer than
     * the version in the database, re-register the roles.
     */
    private function should_roles_be_set() {
        $custom_roles_version = get_option( 'rth_custom_roles_version', 0 );

        return $custom_roles_version < self::VERSION;
    }

    private function remove_roles() {
        remove_role( 'editor' );
        remove_role( 'author' );
        remove_role( 'contributor' );
        remove_role( 'subscriber' );
    }

    private function register_roles() {
        foreach ( $this->roles as $role ) {
            $role->register();
        }
    }
}
