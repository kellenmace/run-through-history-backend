<?php

namespace RunThroughHistory\Interfaces;

interface Hookable {
	/**
	 * Register hooks with WordPress.
	 *
	 * @return void
	 */
	public function register_hooks();
}
