<?php

namespace RunThroughHistory\Admin;

use RunThroughHistory\Interfaces\Hookable;

class AdminAccess implements Hookable {
    public function register_hooks() {
        add_action( 'admin_init', [ $this, 'log_out_and_redirect_non_admins' ] );
    }

    public function log_out_and_redirect_non_admins() {
        if ( $this->is_admin_user() ) {
            return;
        }

        wp_logout();

        // If a frontend app URL is set, send non-admin user there.
        // Otherwise, send them back to the admin login page.
        if ( defined('FRONTEND_APP_URL') ) {
            wp_redirect( FRONTEND_APP_URL );
        } else {
            wp_safe_redirect( admin_url() );
        }

        die();
    }

    private function is_admin_user() {
        return current_user_can('administrator');
    }
}
