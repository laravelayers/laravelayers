<?php

return [

    'menu' => [
        'name' => 'Actions'
    ],

    'columns' => [
        'action' => 'Action',
        'allowed' => 'Allowed',
        'ip' => 'IP restriction',
        'role' => 'Role'
    ],

    'elements' => [
        'action_label' => 'Action',
        'action_help' => 'The action to authorize a user must match the route name '
            . 'and must not contain the "role" prefix, for example: "admin.auth.users". '
            . 'If you add a URL, then it will be converted according to the format of the action. '
            . 'The action can contain the name of the route action method ("view", "create", "update", "delete"), '
            . 'for example: "admin.auth.users.view".
        ',
        'allowed_label' => 'Allowed',
        'ip_label' => 'IP restriction',

        'search_by_action_text' => 'By action',
        'search_by_ip_text' => 'By IP',
        'is_role_label' => 'Roles only',
    ],

    'actions' => [
        //
    ],

    'errors' => [
        //
    ]

];
