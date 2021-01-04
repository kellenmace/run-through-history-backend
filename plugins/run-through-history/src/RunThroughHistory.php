<?php

namespace RunThroughHistory;

/**
 * Main plugin class.
 */
final class RunThroughHistory {
	/**
	 * Class instances.
	 */
	private $instances = [];

	/**
	 * Main method for running the plugin.
	 */
	public function run() {
		$this->create_instances();
		$this->register_hooks();
	}

	private function create_instances() {
		$this->instances['admin_access']            = new Admin\AdminAccess();
		$this->instances['admin_menu_customizer']   = new Admin\AdminMenuCustomizer();
		$this->instances['email_settings']          = new Email\EmailSettings();
		$this->instances['featured_images']         = new Media\FeaturedImages();
		$this->instances['award_post_type']         = new PostTypes\AwardPostType();
		$this->instances['run_post_type']           = new PostTypes\RunPostType();
		$this->instances['age_group_setter']        = new UserMetaSetters\AgeGroupSetter();
		$this->instances['total_miles_setter']      = new UserMetaSetters\TotalMilesSetter();
		$this->instances['award_type_taxonomy']     = new Taxonomies\AwardTypeTaxonomy();
		$this->instances['user_age_group_taxonomy'] = new Taxonomies\UserAgeGroupTaxonomy();
		$this->instances['user_sex_taxonomy']       = new Taxonomies\UserSexTaxonomy();
		$this->instances['runner_role']             = new UserRoles\RunnerRole();
		$this->instances['roles_registrar']         = new UserRoles\RolesRegistrar( $this->instances );
		$this->instances['runs_connection']         = new WPGraphQL\Connection\Runs();
		$this->instances['users_connection']        = new WPGraphQL\Connection\Users();
		$this->instances['user_type']               = new WPGraphQL\Type\Object\User();
		$this->instances['state_enum']              = new WPGraphQL\Type\Enum\StateEnum();
		$this->instances['user_sex_enum']           = new WPGraphQL\Type\Enum\UserSexEnum();
		$this->instances['user_age_group_enum']     = new WPGraphQL\Type\Enum\UserAgeGroupEnum();
		$this->instances['create_run_mutation']     = new WPGraphQL\Mutation\CreateRun();
		$this->instances['user_register_mutation']  = new WPGraphQL\Mutation\UserRegister( $this->instances['age_group_setter'] );
	}

	private function register_hooks() {
		foreach ( $this->get_hookable_instances() as $instance ) {
			$instance->register_hooks();
		}
	}

	private function get_hookable_instances() {
		return array_filter( $this->instances, function( $instance ) {
			return $instance instanceof Interfaces\Hookable;
		} );
	}
}
