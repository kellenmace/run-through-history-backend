<?php

namespace RunThroughHistory\Admin;

use RunThroughHistory\Interfaces\Hookable;

class AdminMenuCustomizer implements Hookable {
    public function register_hooks() {
        add_action( 'admin_menu', [ $this, 'customize_admin_sidebar' ] );
    }

    public function customize_admin_sidebar() {
        // Remove Posts menu item
        remove_menu_page('edit.php');

        // Remove Pages menu item
        remove_menu_page('edit.php?post_type=page');

        // Remove Comments menu item
        remove_menu_page('edit-comments.php');
    }
}
