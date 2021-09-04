<?php

return [

    'menu' => [
        'name' => 'Роли'
    ],

    'columns' => [
        'role' => 'Название',
        'description' => 'Описание'
    ],

    'elements' => [
        'role_label' => 'Название',

        'search_by_name_text' => 'По названию'
    ],

    'actions' => [
        'actions' => 'Авторизованные действия :count',
        'users' => 'Пользователи :count'
    ],

    'errors' => [
        'exists' => 'Роль уже существует.'
    ]

];
