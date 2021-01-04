<?php

namespace RunThroughHistory\Taxonomies;

use WP_User;
use RunThroughHistory\Interfaces\Hookable;

class UserAgeGroupTaxonomy implements Hookable {
    use TaxonomyLabelUtility;
    use UserTaxonomyCountUpdater;
    use UserTaxonomyTermUpdater;

    const KEY         = 'user_age_group';
    const TERM_0_10   = '0_10';
    const TERM_10_20  = '10_20';
    const TERM_20_30  = '20_30';
    const TERM_30_40  = '30_40';
    const TERM_40_50  = '40_50';
    const TERM_50_60  = '50_60';
    const TERM_60_70  = '60_70';
    const TERM_70_120 = '70_120';

    public function register_hooks() {
        add_action( 'init',                     [ $this, 'register' ], 11 );
        add_action( 'admin_init',               [ $this, 'ensure_terms_exist' ] );
        add_action( 'show_user_profile',        [ $this, 'render_profile_fields' ] );
        add_action( 'edit_user_profile',        [ $this, 'render_profile_fields' ] );
        add_action( 'personal_options_update',  [ $this, 'save_terms' ] );
        add_action( 'edit_user_profile_update', [ $this, 'save_terms' ] );
    }

    public function register() {
        /**
         * Do not use this taxonomy for any other object types, such as posts.
         * It must remain a user-only taxonomy.
         *
         * @see https://wordpress.stackexchange.com/questions/277861/share-taxonomy-between-user-and-posts
         */
        register_taxonomy( self::KEY, 'user', [
            'labels'                => $this->generate_labels( 'Age Group', 'Age Group' ),
            'hierarchical'          => true,
            'capabilities'          => [
				'manage_terms' => 'edit_users',
				'edit_terms'   => 'edit_users',
				'delete_terms' => 'edit_users',
				'assign_terms' => 'edit_users',
            ],
            'update_count_callback' => [ $this, 'update_term_count' ],
            'show_in_graphql'       => true,
            'graphql_single_name'   => 'UserAgeGroup',
            'graphql_plural_name'   => 'UserAgeGroups',
        ] );
    }

    public function ensure_terms_exist() {
        $number_of_terms = wp_count_terms( self::KEY );

        if ( 8 === (int) $number_of_terms ) {
            return;
        }

        $this->create_terms();
    }

    private function create_terms() {
        wp_insert_term( 'Under 10',     self::KEY, [ 'slug' => self::TERM_0_10 ] );
        wp_insert_term( '10 to 20',     self::KEY, [ 'slug' => self::TERM_10_20 ] );
        wp_insert_term( '20 to 30',     self::KEY, [ 'slug' => self::TERM_20_30 ] );
        wp_insert_term( '30 to 40',     self::KEY, [ 'slug' => self::TERM_30_40 ] );
        wp_insert_term( '40 to 50',     self::KEY, [ 'slug' => self::TERM_40_50 ] );
        wp_insert_term( '50 to 60',     self::KEY, [ 'slug' => self::TERM_50_60 ] );
        wp_insert_term( '60 to 70',     self::KEY, [ 'slug' => self::TERM_60_70 ] );
        wp_insert_term( '70 or Better', self::KEY, [ 'slug' => self::TERM_70_120 ] );
    }

    /**
     * Render fields on the user/profile page in the admin.
     *
     * @param WP_User $user The user object currently being edited.
     */
    public function render_profile_fields( WP_User $user ) {
        $tax = get_taxonomy( self::KEY );

        // Make sure the user can assign terms before proceeding.
        if ( ! current_user_can( $tax->cap->assign_terms ) ) {
            return;
        }
    
        $terms = get_terms( self::KEY, [ 'hide_empty' => false ] );
    
        if ( ! is_array( $terms ) ) {
            return;
        }
    
        ?>
        <h3><?php _e( 'Age Group', 'run-through-history' ); ?></h3>
        <?php

        if ( ! $terms ) {
            ?>
            <p><?php esc_html_e( 'No user age groups found.', 'run-through-history' ); ?></p>
            <?php

            return;
        }

        // Sort terms by slug.
        usort( $terms, function( $term_a, $term_b ) {
            return strcmp( $term_a->slug, $term_b->slug );
        } );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="user_age_group"><?php esc_html_e( 'Select Age Group', 'run-through-history' ); ?></label></th>
                <td>
                    <?php foreach ( $terms as $term ) : ?>
                        <input
                            type="radio"
                            name="user_age_group"
                            id="user_age_group-<?php echo esc_attr( $term->slug ); ?>"
                            value="<?php echo esc_attr( $term->slug ); ?>"
                            <?php checked( true, is_object_in_term( $user->ID, self::KEY, $term->term_id ) ); ?>
                        />
                        <label for="user_age_group-<?php echo esc_attr( $term->slug ); ?>"><?php echo $term->name; ?></label>
                        <br />
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
        <?php
    }
}
