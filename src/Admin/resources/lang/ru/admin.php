<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Языковые ресусры администрирования
    |--------------------------------------------------------------------------
    |
    | Следующие языковые строки содержат текст для администрирования.
    |
    */

    'menu' => [
        'name' => 'Администрирование',
        'docs' => 'Laravelayers',
        'id' => '№ :id',
        'search' => 'Поиск'
    ],

    'columns' => [
        'id' => 'ИД',
        'name' => 'Название',
        'description' => 'Описание',
        'image' => 'Изображение',
        'status' => 'Статус',
        'created_at' => 'Создано',
        'updated_at' => 'Обновлено'
    ],

    'elements' => [
        'filter_link_placeholder' => 'Поиск',
        'filter_reset_link_text' => 'Сбросить фильтр',

        'search_group' => 'Поиск',
        'search_by_id_text' => 'По ИД',
        'search_by_name_text' => 'По названию',
        'search_by_description_text' => 'По описанию',

        'filter_group' => 'Фильтр',

        'pagination_group' => 'Количество элементов на странице',

        'id_label' => 'ИД',
        'name_label' => 'Название',
        'description_label' => 'Описание',
        'image_label' => 'Изображение',
        'status_label' => 'Статус',
        'created_at_label' => 'Дата создания',
        'updated_at_label' => 'Дата обновления',
        'yes_text' => 'Да',
        'no_text' => 'Нет',

        'replacement_group' => 'Замена по шаблону',
        'pattern_label' => 'Шаблон',
        'replacement_label' => 'Замена',

        'save_and_continue_button' => 'Сохранить и продолжить',
        'save_and_go_back_button' => 'Сохранить и вернуться назад',
        'save_button' => 'Сохранить',
        'cancel_button' => 'Отменить',
        'delete_button' => 'Удалить',
        'go_back_button' => 'Вернуться назад',
    ],

    'actions' => [
        'text' => 'Действия',
        'add' => 'Добавить',
        'create' => 'Создать',
        'copy' => 'Копировать',
        'edit' => 'Редактировать',
        'select' => 'Выбрать',
        'show' => 'Показать',
        'add_multiple' => 'Добавить выбранные',
        'edit_multiple' => 'Редактировать выбранные',
        'delete_multiple' => 'Удалить выбранные',
    ],

    'alerts' => [
        'not_found' => 'Данные не найдены.'
    ],

];
