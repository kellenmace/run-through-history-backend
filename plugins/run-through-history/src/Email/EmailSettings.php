<?php

namespace RunThroughHistory\Email;

use WP_User;
use RunThroughHistory\Interfaces\Hookable;

class EmailSettings implements Hookable {
    public function register_hooks() {
        add_filter( 'wp_mail_from',                   [ $this, 'modify_email_from_address'] );
        add_filter( 'wp_mail_from_name',              [ $this, 'get_site_name' ] );
        add_filter( 'retrieve_password_title',        [ $this, 'modify_email_reset_subject'] );
        add_filter( 'retrieve_password_message',      [ $this, 'modify_password_reset_message' ], 10, 4 );
        add_filter( 'wp_new_user_notification_email', [ $this, 'modify_new_user_notification_email'], 10, 3 );
    }

    /**
     * Note: The server domain name must be used for the 'from' address. Otherwise,
     * sent emails will be considered spam and will be rejected.
     *
     * @return string The new 'From' email address for emails.
     */
    public function modify_email_from_address() : string {
        // Get the site domain and get rid of www, if present.
        $sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
        if ( 'www.' === substr( $sitename, 0, 4 ) ) {
            $sitename = substr( $sitename, 4 );
        }

        return 'info@' . $sitename;
    }

    private function get_site_name() : string {
        if ( is_multisite() ) {
            return get_network()->site_name;
        }

        /*
         * The blogname option is escaped with esc_html on the way into the database
         * in sanitize_option we want to reverse this for the plain text arena of emails.
         */
        return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    }

    public function modify_email_reset_subject() : string{
        return $this->get_site_name() . ' - Password Reset';
	}

	public function modify_password_reset_message( string $message, string $key, string $user_login, WP_User $user_data ) : string {
		$message = "A password reset has been requested for the following {$this->get_site_name()} account: {$user_data->user_email}." . PHP_EOL . PHP_EOL;
		$message .= 'If you did not request a password reset, you can safely ignore this email.' . PHP_EOL . PHP_EOL;
		$message .= 'You can reset your password here:' . PHP_EOL;
		$message .= $this->get_set_password_url( $key, $user_login );

		return $message;
	}

	private function get_set_password_url( string $key, string $user_login, bool $new_account = false ) : string {
        $login_encoded = rawurlencode( $user_login );
        $frontend_url  = defined( 'FRONTEND_APP_URL' ) ? FRONTEND_APP_URL : '';

        $url = untrailingslashit( $frontend_url ) . "/set-password/?key={$key}&login={$login_encoded}";

        if ( $new_account ) {
            $url .= '&new_account=true';
        }

        return $url;
    }

    /**
     * Filters the contents of the new user notification email sent to the new user.
     *
     * @param array   $wp_new_user_notification_email {
     *     Used to build wp_mail().
     *
     *     @type string $to      The intended recipient - New user email address.
     *     @type string $subject The subject of the email.
     *     @type string $message The body of the email.
     *     @type string $headers The headers of the email.
     * }
     * @param WP_User $user     User object for new user.
     * @param string  $blogname The site title.
     */
    public function modify_new_user_notification_email( array $wp_new_user_notification_email, WP_User $user, string $blogname ) : array {
        $sitename = $this->get_site_name();
        $key      = get_password_reset_key( $user );

        $wp_new_user_notification_email['subject'] = $sitename . ' - Sign Up';

        $message = "Welcome! You have successfully signed up for a {$this->get_site_name()} account." . PHP_EOL . PHP_EOL;
        $message .= 'You can set a password and sign into your new account here:' . PHP_EOL;
        $message .= $this->get_set_password_url( $key, $user->user_login, true );

        $wp_new_user_notification_email['message'] = $message;

        return $wp_new_user_notification_email;
    }
}
