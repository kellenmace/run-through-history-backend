<?php

namespace RunThroughHistory\UserRoles;

use RunThroughHistory\Interfaces\UserRole;

class RunnerRole implements UserRole {
    const NAME = 'runner';

    public function register() {
        add_role(
            self::NAME,
            'Runner',
            [
                'upload_files' => true,
                'edit_posts' => true,
                'edit_published_posts' => true,
                'publish_posts' => true,
                'read' => true,
                'level_2' => true,
                'level_1' => true,
                'level_0' => true,
                'delete_posts' => true,
                'delete_published_posts' => true,
            ]
        );
    }
}
