<?php

return [

    'menu' => [
        'name' => 'Действия'
    ],

    'columns' => [
        'action' => 'Действие',
        'allowed' => 'Разрешено',
        'ip' => 'Ограничение по IP',
        'role' => 'Роль'
    ],

    'elements' => [
        'action_label' => 'Действие',
        'action_help' => 'Действие для авторизации пользователя должно соответствовать имени маршрута '
            . 'и не должно содержать префикс "role", например: "admin.auth.users". '
            . 'Если добавить URL, то он будет преобразован в соответствии с форматом действия. '
            . 'Действие может содержать имя метода действия маршрута ("view", "create", "update", "delete"), '
            . 'например: "admin.auth.users.view".',
        'allowed_label' => 'Разрешено',
        'ip_label' => 'Ограничение по IP',

        'search_by_action_text' => 'По действию',
        'search_by_ip_text' => 'По IP',
        'is_role_label' => 'Только роли',
    ],

    'actions' => [
        //
    ],

    'errors' => [
        //
    ]

];
