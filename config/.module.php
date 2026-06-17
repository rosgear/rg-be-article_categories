<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'   => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: read, add, edit, delete, clear)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Grid'    => ['data', 'view', 'update', 'delete', 'clear', 'filter', 'move'],
                'Form'    => ['data', 'view', 'add', 'update', 'delete'],
                'Search'  => ['data', 'view'],
                'Trigger' => ['combo'],
                'Move'    => ['perform', 'data', 'view']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Просмотр" (view)
            'allow',
            'permission'  => 'view',
            'controllers' => [
                'Grid'    => ['data', 'view', 'filter'],
                'Form'    => ['data', 'view'],
                'Search'  => ['data', 'view'],
                'Trigger' => ['combo']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Чтение" (read)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Grid'    => ['data'],
                'Form'    => ['data'],
                'Search'  => ['data'],
                'Trigger' => ['combo']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Добавление" (add)
            'allow',
            'permission'  => 'add',
            'controllers' => [
                'Form' => ['add']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Изменение" (edit)
            'allow',
            'permission'  => 'edit',
            'controllers' => [
                'Grid' => ['update'],
                'Form' => ['update']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Удаление" (delete)
            'allow',
            'permission'  => 'delete',
            'controllers' => [
                'Grid' => ['delete'],
                'Form' => ['delete']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Очистка" (clear)
            'allow',
            'permission'  => 'clear',
            'controllers' => [
                'Grid' => ['clear']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // разрешение "Настройка модуля" (settings)
            'allow',
            'permission'  => 'settings',
            'controllers' => ['Settings'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'viewManager' => [
        'id'          => 'rg-acategories-{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
            'form' => '/form.json',
            'move' => '/move.json'
        ]
    ]
];
