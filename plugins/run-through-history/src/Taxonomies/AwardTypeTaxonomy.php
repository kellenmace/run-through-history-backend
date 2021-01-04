<?php

namespace RunThroughHistory\Taxonomies;

use RunThroughHistory\Interfaces\Hookable;
use RunThroughHistory\Interfaces\Taxonomy;
use RunThroughHistory\PostTypes\AwardPostType;

class AwardTypeTaxonomy implements Hookable, Taxonomy {
    use TaxonomyLabelUtility;

    const KEY        = 'award_type';
    const BADGE_TERM = 'badge';
    const MEDAL_TERM = 'medal';

    public function register_hooks() {
        add_action( 'init',       [ $this, 'register' ], 11 );
        add_action( 'admin_init', [ $this, 'ensure_terms_exist' ] );
    }

    public function register() {
        register_taxonomy( self::KEY, AwardPostType::KEY, [
            'labels'              => $this->generate_labels( 'Award Type', 'Award Type' ),
            'hierarchical'        => true,
            'show_admin_column'   => true,
            'show_in_graphql'     => true,
            'graphql_single_name' => 'AwardType',
            'graphql_plural_name' => 'AwardTypes',
        ] );
    }

    public function ensure_terms_exist() {
        $number_of_terms = wp_count_terms( self::KEY );

        if ( 2 === (int) $number_of_terms ) {
            return;
        }

        $this->create_terms();
    }

    private function create_terms() {
        wp_insert_term( 'Badge', self::KEY, [ 'slug' => self::BADGE_TERM ] );
        wp_insert_term( 'Medal', self::KEY, [ 'slug' => self::MEDAL_TERM ] );
    }
}
