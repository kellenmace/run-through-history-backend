<?php

namespace RunThroughHistory\Taxonomies;

use WP_User;
use RunThroughHistory\Interfaces\Hookable;

class UserSexTaxonomy implements Hookable {
    use TaxonomyLabelUtility;
    use UserTaxonomyCountUpdater;
    use UserTaxonomyTermUpdater;

    const KEY         = 'user_sex';
    const FEMALE_TERM = 'female';
    const MALE_TERM   = 'male';

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
            'labels'                => $this->generate_labels( 'Sex', 'Sex' ),
            'hierarchical'          => true,
            'capabilities'          => [
				'manage_terms' => 'edit_users',
				'edit_terms'   => 'edit_users',
				'delete_terms' => 'edit_users',
				'assign_terms' => 'edit_users',
            ],
            'update_count_callback' => [ $this, 'update_term_count' ],
            'show_in_graphql'       => true,
            'graphql_single_name'   => 'UserSex',
            'graphql_plural_name'   => 'UserSexes',
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
        wp_insert_term( 'Female', self::KEY, [ 'slug' => self::FEMALE_TERM ] );
        wp_insert_term( 'Male',   self::KEY, [ 'slug' => self::MALE_TERM ] );
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
        <h3><?php _e( 'Sex', 'run-through-history' ); ?></h3>
        <?php

        if ( ! $terms ) {
            ?>
            <p><?php esc_html_e( 'No user sexes found.', 'run-through-history' ); ?></p>
            <?php

            return;
        }

        ?>
        <table class="form-table">
            <tr>
                <th><label for="user_sex"><?php esc_html_e( 'Select Sex', 'run-through-history' ); ?></label></th>
                <td>
                    <?php foreach ( $terms as $term ) : ?>
                        <input
                            type="radio"
                            name="user_sex"
                            id="user_sex-<?php echo esc_attr( $term->slug ); ?>"
                            value="<?php echo esc_attr( $term->slug ); ?>"
                            <?php checked( true, is_object_in_term( $user->ID, self::KEY, $term->term_id ) ); ?>
                        />
                        <label for="user_sex-<?php echo esc_attr( $term->slug ); ?>"><?php echo $term->name; ?></label>
                        <br />
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
        <?php
    }
}
