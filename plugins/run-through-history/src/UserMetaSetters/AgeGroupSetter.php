<?php

namespace RunThroughHistory\UserMetaSetters;

use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Taxonomies\UserAgeGroupTaxonomy;

class AgeGroupSetter implements Hookable {
    public function register_hooks() {
        add_action( 'profile_update', [ $this, 'set' ] );
    }

    /**
     * Use the user's date of birth to set their Age Group.
     */
    public function set( int $user_id ) : void {
        $date_of_birth = get_user_meta( $user_id, 'date_of_birth', true );

        if ( ! $date_of_birth || ! defined( 'EVENT_START_DATE' ) ) {
            return;
        }

        $age = date_diff( date_create( $date_of_birth ), date_create( EVENT_START_DATE ) )->y;

        if ( $age < 10 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_0_10, UserAgeGroupTaxonomy::KEY );
            return;
        }

        if ( $age < 20 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_10_20, UserAgeGroupTaxonomy::KEY );
            return;
        }

        if ( $age < 30 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_20_30, UserAgeGroupTaxonomy::KEY );
            return;
        }

        if ( $age < 40 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_30_40, UserAgeGroupTaxonomy::KEY );
            return;
        }

        if ( $age < 50 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_40_50, UserAgeGroupTaxonomy::KEY );
            return;
        }

        if ( $age < 60 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_50_60, UserAgeGroupTaxonomy::KEY );
            return;
        }

        if ( $age < 70 ) {
            wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_60_70, UserAgeGroupTaxonomy::KEY );
            return;
        }

        // 70 or better.
        wp_set_object_terms( $user_id, UserAgeGroupTaxonomy::TERM_70_120, UserAgeGroupTaxonomy::KEY );
    }
}
