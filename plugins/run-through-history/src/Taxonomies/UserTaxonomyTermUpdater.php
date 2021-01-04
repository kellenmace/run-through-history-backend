<?php

namespace RunThroughHistory\Taxonomies;

/**
 * Utility method for saving a single user taxonomy term.
 */
trait UserTaxonomyTermUpdater {
    /**
     * Save the term selected on the edit user/profile page in the admin.
     *
     * @see http://justintadlock.com/archives/2011/10/20/custom-user-taxonomies-in-wordpress
     *
     * @param int $user_id The ID of the user to save the terms for.
     */
    public function save_terms( int $user_id ) {
        $term = $_POST[ self::KEY ] ?? null;

        if ( ! $term ) {
            return;
        }

        $tax = get_taxonomy( self::KEY );

        // Make sure the current user can edit the user and assign terms before proceeding.
        if ( ! current_user_can( 'edit_user', $user_id ) && current_user_can( $tax->cap->assign_terms ) ) {
            return;
        }

        wp_set_object_terms( $user_id, [ esc_attr( $term ) ], self::KEY, false );

        clean_object_term_cache( $user_id, self::KEY );
    }
}
