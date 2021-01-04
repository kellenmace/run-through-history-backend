<?php

namespace RunThroughHistory\PostTypes;

trait PostTypeLabelUtility {
    /**
     * Generate labels for a post type.
	 *
	 * @param string $singular          Uppercase, singular label.
	 * @param string $plural            Uppercase, plural label.
	 * @param array  $additional_labels Additional labels.
	 *
	 * @return array Labels.
     */
	protected function generate_labels( string $singular, string $plural, array $additional_labels = [] ) : array {
		$labels = [
			'name'                  => _x( $plural, 'post type general name', 'run-through-history' ),
			'singular_name'         => _x( $singular, 'post type singular name', 'run-through-history' ),
			'menu_name'             => _x( $plural, 'admin menu', 'run-through-history' ),
			'name_admin_bar'        => _x( $singular, 'add new on admin bar', 'run-through-history' ),
			'add_new'               => _x( 'Add New', $singular, 'run-through-history' ),
			'add_new_item'          => __( "Add New {$singular}", 'run-through-history' ),
			'new_item'              => __( "New {$singular}", 'run-through-history' ),
			'edit_item'             => __( "Edit {$singular}", 'run-through-history' ),
			'view_item'             => __( "View {$singular}", 'run-through-history' ),
			'all_items'             => __( "All {$plural}", 'run-through-history' ),
			'search_items'          => __( "Search {$plural}", 'run-through-history' ),
			'parent_item_colon'     => __( "Parent {$plural}:", 'run-through-history' ),
			'not_found'             => __( "No {$plural} found.", 'run-through-history' ),
			'not_found_in_trash'    => __( "No {$plural} found in Trash.", 'run-through-history' ),
			'archives'              => __( "{$singular} Archives", 'run-through-history' ),
            'update_item'           => __( "Update {$singular}", 'run-through-history' ),
            'insert_into_item'      => __( "Insert into {$singular}", 'run-through-history' ),
            'uploaded_to_this_item' => __( "Uploaded to this {$singular}", 'run-through-history' ),
            'items_list'            => __( "{$plural} list", 'run-through-history' ),
            'items_list_navigation' => __( "{$plural} list navigation", 'run-through-history' ),
            'filter_items_list'     => __( "Filter {$plural} list", 'run-through-history' ),
		];

		return array_merge( $labels, $additional_labels );
	}
}
