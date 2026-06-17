<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    '{name}'        => 'Website article categories',
    '{description}' => 'Managing categories of website articles',
    '{permissions}' => [
        'any'    => ['Full access', 'View and make changes to site article categories'],
        'view'   => ['View', 'View Site article categories'],
        'read'   => ['Reading', 'Reading site article categories'],
        'add'    => ['Adding', 'Adding site article categories'],
        'edit'   => ['Editing', 'Editing site article categories'],
        'delete' => ['Deleting', 'Deleting site article categories'],
        'clear'  => ['Clear', 'Deleting all site article categories']
    ],

    // Form
    '{appendTo.title}' => 'Add an article category to a section "{name}"',
    '{form.title}' => 'Add an article category ',
    '{form.titleTpl}' => 'Edit the category of an article "{name}"',
    // Form: поля
    'Name' => 'Name',
    'Parent category' => 'Parent category',
    'If the category is a subcategory, then you must indicate who it belongs to' 
        => 'If the category is a subcategory, then you must indicate who it belongs to',
    'Index' => 'Index',
    'Index number' => 'Index number',
    'SEF URL category' => 'SEF URL category',
    'Language' => 'Language',
    'The language in which the category will be accessed on the site' => 'The language in which the category will be accessed on the site',
    'Publish' => 'Publish',
    'Main page' => 'Main page',
    'creating a main page for a category' 
        => 'creating a main page for a category',
    'The index number is used to order the categories in the lists' 
        => 'The index number is used to order the categories in the lists.',
    'The Slug is a version of a name, a unique part of a URL. These are all lowercase letters and only Latin letters, numbers and hyphens.' 
        => 'The Slug is a version of a name, a unique part of a URL. These are all lowercase letters and only Latin letters, numbers and hyphens.',
    'to display the main page of a category, you need to create article (record) with the type "Main page" or set the flag "Main page"' 
        => 'to display the main page of a category, you need to create article (record) with the type "Main page" or set the flag "Main page".',
    'Article type' => 'Article type',
    'Type of added material for the main page of the category' => 'Type of added material for the main page of the category',

    // Move
    '{move.title}' => 'Move article category ',
    '{move.titleTpl}' => 'Move article category "{name}"',
    'Apply' => 'Apply',
    'Move to' => 'Move to ',
    // Move: сообщения / загаловок
    'Moving' => 'Moving',
    // Move: сообщения / текст
    'Article category successfully moved' => 'Article category successfully moved.',
    'You agree to move "{0}" to "{1}"?' => 'You agree to move "{0}" to "{1}"?',
    // Move: сообщения / ошибки
    'Unable to move the category to the section you selected' => 'Unable to move the category to the section you selected',
    
    // Grid: контекстное меню записи
    'Edit category' => 'Edit category',
    'Add category to section' => 'Add category to section',
    'Move category' => 'Move category',
    // Grid: столбцы
    'Category is available and published' => 'Category is available and published',
    'Number of subcategories' => 'Number of subcategories',
    'Path' => 'Path',
    'The path is the unique part of the URL. Consists of a category slug and subcategory slug (if any). Created automatically.' 
        => 'The path is the unique part of the URL. Consists of a category slug and subcategory slug (if any). Created automatically.',
    'View category' => 'View category',
    'Slug' => 'Slug',
    'Published' => 'Published',
    'yes' => 'yes',
    'no' => 'no',
    // Grid: сообщения / загаловки
    'Publication' => 'Publication',
    // Grid: сообщения / текст
    'Article category - unpublished' => 'Article category - <b>unpublished</b>.',
    'Article category - published' => 'Article category - <b>published</b>.',
    // Grid: ошибки
    'Categories have not been deleted, because there was an error in updating articles (records) of the site' 
        => 'Categories have not been deleted, because there was an error in updating articles (records) of the site.',
    'Error while deleting selected categories' => 'Error while deleting selected categories.',
    'The category you selected does not exist' => 'The category you selected does not exist.',

    // GridRow: журнал аудита
    'article category with id {0} is unpublished' => 'article category with id "<b>{0}</b>" is unpublished',
    'article category with id {0} is published' => 'article category with id "<b>{0}</b>" is published'
];
