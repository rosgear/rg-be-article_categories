<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации Карты SQL-запросов.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'drop'   => ['{{article_category}}'],
    'create' => [
        '{{article_category}}' => function () {
            return "CREATE TABLE `{{article_category}}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `language_id` int(11) unsigned DEFAULT NULL,
                `index` int(11) unsigned DEFAULT '1',
                `name` varchar(255) DEFAULT NULL,
                `publish` tinyint(1) unsigned DEFAULT '1',
                `slug` varchar(255) DEFAULT NULL,
                `slug_path` text DEFAULT NULL,
                `slug_hash` varchar(32) DEFAULT NULL,
                `ns_left` int(11) unsigned DEFAULT NULL,
                `ns_right` int(11) unsigned DEFAULT NULL,
                `ns_parent` int(11) unsigned DEFAULT NULL,
                `_updated_date` datetime DEFAULT NULL,
                `_updated_user` int(11) unsigned DEFAULT NULL,
                `_created_date` datetime DEFAULT NULL,
                `_created_user` int(11) unsigned DEFAULT NULL,
                `_lock` tinyint(1) unsigned DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE={engine} 
            DEFAULT CHARSET={charset} COLLATE {collate}";
        }
    ],
    'insert' => [
        '{{term}}' => [
            [
                'name'           => 'article_category',
                'component_id'   => 'rg.be.article_categories',
                'component_type' => 'module'
            ]
        ]
    ],
    'delete' => [
        '{{term}}' => [
            [
                'name'         => 'article_category',
                'component_id' => 'rg.be.article_categories'
            ]
        ]
    ],

    'run' => [
        'install'   => ['drop', 'create', 'insert'],
        'uninstall' => ['drop']
    ]
];