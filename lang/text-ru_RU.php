<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Пакет русской локализации.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    '{name}'        => 'Категории материала веб-сайта',
    '{description}' => 'Управление категориями материалов (статей) веб-сайта',
    '{permissions}' => [
        'any'    => ['Полный доступ', 'Просмотр и внесение изменений в категории материала'],
        'view'   => ['Просмотр', 'Просмотр категорий материала'],
        'read'   => ['Чтение', 'Чтение категорий материала'],
        'add'    => ['Добавление', 'Добавление категорий материала'],
        'edit'   => ['Изменение', 'Изменение категорий материала'],
        'delete' => ['Удаление', 'Удаление категорий материала'],
        'clear'  => ['Очистка', 'Удаление всех категорий материала']
    ],

    // Form
    '{appendTo.title}' => 'Добавление категории материала в раздел "{name}"',
    '{form.title}' => 'Добавление категории материала ',
    '{form.titleTpl}' => 'Изменение категории "{name}"',
    // Form: поля
    'Name' => 'Название',
    'Parent category' => 'Раздел категории',
    'If the category is a subcategory, then you must indicate who it belongs to' 
        => 'Если категория является подкатегорией, то необходимо указать кому она принадлежит',
    'Index' => 'Порядок',
    'Index number' => 'Порядквый номер',
    'SEF URL category' => 'ЧПУ URL категории',
    'Language' => 'Язык',
    'The language in which the category will be accessed on the site' 
        => 'Язык на котором будет доступа категория на сайте',
    'Publish' => 'Опубликовать',
    'Main page' => 'Главная страница',
    'creating a main page for a category' 
        => 'добавить материал в виде главной страницы категории',
    'The index number is used to order the categories in the lists' 
        => 'Порядковый номер используется для упорядочивания категорий в списках (по умолчанию - 1).',
    'The Slug is a version of a name, a unique part of a URL. These are all lowercase letters and only Latin letters, numbers and hyphens.' 
        => 'Ярлык (слаг) - это версия имени, уникальная часть URL-адреса. Это все строчные буквы и только буквы на латинице, цифры и дефисы. Если не указан, он будет создан автоматически из названия.',
    'to display the main page of a category, you need to create article (record) with the type "Main page" or set the flag "Main page"' 
        => 'Для отображения главной страницы категории, необходимо создать материал с видом ярлыка "Основной" или выставить флаг "Главная страница".',
    'Article type' => 'Тип материала',
    'Type of added material for the main page of the category' => 'Тип добавляемого материала для главной страницы категории',

    // Move
    '{move.title}' => 'Перемещение категории материала ',
    '{move.titleTpl}' => 'Перемещение категории "{name}"',
    'Apply' => 'Применить',
    'Move to' => 'Переместить в ',
    // Move: сообщения / загаловок
    'Moving' => 'Перемещение',
    // Move: сообщения / текст
    'Article category successfully moved' => 'Категория материала успешно перемещена.',
    'You agree to move "{0}" to "{1}"?' => 'Вы согласны переместить категорию материала "{0}" в "{1}"?',
    // Move: сообщения / ошибки
    'Unable to move the category to the section you selected' => 'Невозможно переместить категорию в выбранный вами раздел',
    
    // Grid: контекстное меню записи
    'Edit category' => 'Редактировать',
    'Add category to section' => 'Добавить категорию в раздел',
    'Move category' => 'Переместить категорию',
    // Grid: столбцы
    'Category is available and published' => 'Публикация и доступность категории материала',
    'Number of subcategories' => 'Количество подкатегорий',
    'Path' => 'Путь',
    'The path is the unique part of the URL. Consists of a category slug and subcategory slug (if any). Created automatically.' 
        => 'Путь - это уникальная часть URL-адреса. Состоит из ярлыка категории и ярлыков подкатегорий (если они имеются). Создаётся автоматически.',
    'View category' => 'Просмотреть категорию',
    'Slug' => 'Ярлык',
    'Published' => 'Опубликована',
    'yes' => 'да',
    'no' => 'нет',
    // Grid: сообщения / загаловки
    'Publication' => 'Публикация',
    // Grid: сообщения / текст
    'Article category - unpublished' => 'Категория материала - <b>снята с публикации</b>.',
    'Article category - published' => 'Категория материала - <b>опубликована</b>.',
    // Grid: ошибки
    'Categories have not been deleted, because there was an error in updating articles (records) of the site' 
        => 'Категории не удалены, т.к. возникла ошибка в обновлении материалов сайта.',
    'Error while deleting selected categories' => 'Ошибка при выполнении удаления выбранных категорий.',
    'The category you selected does not exist' => 'Выбранная вами категория не существует.',

    // GridRow: журнал аудита
    'article category with id {0} is unpublished' => 'категория материала c идентификатором "<b>{0}</b>" снята с публикации',
    'article category with id {0} is published' => 'категория материала c идентификатором "<b>{0}</b>" опубликована'
];
