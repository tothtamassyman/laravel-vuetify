<?php

return [
    "auth" => [
        "login_successful" => "Login successful.",
        "login_failed" => "Login failed.",
        "logout_successful" => "Logout successful.",
    ],
    "group" => [
        "created" => "Group created successfully.",
        "updated" => "Group updated successfully.",
        "deleted" => "Group deleted successfully.",
        "user_added" => "User added to the group successfully.",
        "user_removed" => "User removed from the group successfully.",
        "user_cannot_remove_yourself_from_the_group" => "You cannot remove yourself from the group.",
        "cannot_delete_group_with_users" => "This group cannot be deleted because users are still associated with it",
        "no_group_associated_with_user" => "No group associated with user",
    ],
    "user" => [
        "default_group_id_required_on_create'" => "Cannot create user without a default group.",
        "default_group_id_required_on_update" => "Default group cannot be unset.",
    ],
    "permission" => [
        "created" => "Permission created successfully.",
        "updated" => "Permission updated successfully.",
        "deleted" => "Permission deleted successfully.",
    ],
];
