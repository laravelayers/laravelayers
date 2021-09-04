<?php

return [

    'menu' => [
        'name' => 'Roles'
    ],

    'columns' => [
        'role' => 'Name',
        'description' => 'Description'
    ],

    'elements' => [
        'role_label' => 'Name',

        'search_by_name_text' => 'By name'
    ],

    'actions' => [
        'actions' => 'Authorized actions :count',
        'users' => 'Users :count'
    ],

    'errors' => [
        'exists' => 'The role already exists.'
    ]

];
