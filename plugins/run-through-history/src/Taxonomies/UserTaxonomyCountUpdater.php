<?php

namespace RunThroughHistory\Taxonomies;

use WP_Taxonomy;

trait UserTaxonomyCountUpdater {
    /**
     * Update the taxonomy count.
     *
     * @see http://justintadlock.com/archives/2011/10/20/custom-user-taxonomies-in-wordpress
     *
     * @param array       $terms    List of Term taxonomy IDs.
     * @param WP_Taxonomy $taxonomy Taxonomy object.
     */
    public function update_term_count( array $terms, WP_Taxonomy $taxonomy ) {
        global $wpdb;

        foreach ( $terms as $term ) {
            $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term ) );

            do_action( 'edit_term_taxonomy', $term, $taxonomy );
            $wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), [ 'term_taxonomy_id' => $term ] );
            do_action( 'edited_term_taxonomy', $term, $taxonomy );
        }
    }
}
