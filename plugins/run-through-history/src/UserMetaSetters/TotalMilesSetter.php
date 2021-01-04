<?php

namespace RunThroughHistory\UserMetaSetters;

use WP_Query;
use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\PostTypes\RunPostType;

class TotalMilesSetter implements Hookable {
    public function register_hooks() {
        add_action( 'user_register', [ $this, 'initialize_total_miles' ] );
        add_action( 'save_post',     [ $this, 'set_user_total_miles' ] );
        add_action( 'deleted_post',  [ $this, 'set_user_total_miles' ] );
    }

    /**
     * Set user's total miles to 0 on registration.
     */
    public function initialize_total_miles( int $user_id ) : void {
        update_user_meta( $user_id, 'total_miles', 0 );
    }

    /**
     * Recalculate and set a user's total miles whenever they create/update/delete a run.
     */
    public function set_user_total_miles( int $post_id ) : void {
        if ( ! $this->is_run_post( $post_id ) ) {
            return;
        }

        $user_id     = get_post_field( 'post_author', $post_id );
        $total_miles = $this->calculate_total_miles( $user_id );

        update_user_meta( $user_id, 'total_miles', $total_miles );
    }

    private function is_run_post( int $post_id ) : bool {
        return RunPostType::KEY === get_post_type( $post_id );
    }

    /**
     * Add up the miles for all of this user's runs.
     */
    private function calculate_total_miles( int $user_id ) : float {
        $all_run_ids = ( new WP_Query( [
            'post_type'              => RunPostType::KEY,
            'author'                 => $user_id,
            'posts_per_page'         => 1000,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ] ) )->get_posts();

        $all_run_miles = array_map( function( $post_id ) {
            return get_post_meta( $post_id, 'miles', true );
        }, $all_run_ids );

        return array_sum( $all_run_miles );
    }
}
