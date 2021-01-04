<?php

namespace RunThroughHistory\Taxonomies;

trait TaxonomyLabelUtility {
    /**
     * Generate labels for a taxonomy.
	 *
	 * @param string $singular          Uppercase, singular label.
	 * @param string $plural            Uppercase, plural label.
	 * @param array  $additional_labels Additional labels.
     *
     * @return array Labels.
     */
    protected function generate_labels( string $singular, string $plural, array $additional_labels = [] ) : array {
        $labels = [
            'name'              => _x( $plural, 'taxonomy general name', 'run-through-history' ),
            'singular_name'     => _x( $singular, 'taxonomy singular name', 'run-through-history' ),
            'search_items'      => __( "Search {$plural}", 'run-through-history' ),
            'all_items'         => __( "All {$plural}", 'run-through-history' ),
            'parent_item'       => __( "Parent {$singular}", 'run-through-history' ),
            'parent_item_colon' => __( "Parent {$singular}:", 'run-through-history' ),
            'edit_item'         => __( "Edit {$singular}", 'run-through-history' ),
            'update_item'       => __( "Update {$singular}", 'run-through-history' ),
            'add_new_item'      => __( "Add New {$singular}", 'run-through-history' ),
            'new_item_name'     => __( "New {$singular} Name", 'run-through-history' ),
            'menu_name'         => __( $plural, 'run-through-history' ),
        ];

        return array_merge( $labels, $additional_labels );
    }
}
