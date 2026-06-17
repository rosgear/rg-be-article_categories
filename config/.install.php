<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'rg.be.article_categories',
    'name'        => 'Website article categories',
    'description' => 'Managing categories of website articles',
    'namespace'   => 'Rg\Backend\ArticleCategories',
    'path'        => '/rg/rg.be.article_categories',
    'route'       => 'article-categories',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'      => 'rg.be.article_categories',
                'route'       => 'article-categories',
                'prefix'      => BACKEND,
                'constraints' => ['id'],
                'defaults'    => [
                    'controller' => 'grid'
                ]
            ]
        ]
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['any', 'view', 'read', 'add', 'edit', 'delete', 'clear', 'recordRls', 'viewAudit',  'writeAudit', 'info'],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'RG CMS']
    ]
];
