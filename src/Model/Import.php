<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories\Model;

/**
 * Импорт данных.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class Import extends \Ge\Import\Import
{
    /**
     * {@inheritdoc}
     */
    protected string $modelClass = '\Rg\Backend\ArticleCategories\Model\Category';

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            // идентификатор
            'id' => [
                'field' => 'id', 
                'type'  => 'int'
            ],
            // идентификатор языка
            'language_id' => [
                'field' => 'language_id', 
                'type'  => 'int'
            ],
            // порядковы номер
            'index' => [
                'field' => 'index', 
                'type'  => 'int'
            ],
            // название
            'name' =>  [
                'field'  => 'name',
                'length' => 255,
                'trim'   => true
            ],
            // опубликовать
            'publish' => [
                'field' => 'publish', 
                'type'  => 'int'
            ],
            // слаг
            'slug' => [
                'field'  => 'slug', 
                'length' => 255,
                'trim'   => true
            ],
            // слаг (полный путь)
            'slug_path' => [
                'field' => 'slug_path',
                'trim'  => true
            ],
            // хэш пути
            'slug_hash' => [
                'field'  => 'slug_hash',
                'length' => 32,
                'trim'   => true
            ],
            // граница дерева слева
            'ns_left' => [
                'field' => 'ns_left', 
                'type'  => 'int'
            ],
            // граница дерева справа
            'ns_right' => [
                'field' => 'ns_right', 
                'type'  => 'int'
            ],
            // идентификатор родительского узла
            'ns_parent' => [
                'field' => 'ns_parent', 
                'type'  => 'int'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function afterImportAttributes(array $columns): array
    {
        // хэш пути (слага)
        if (!empty($columns['slug_path'])) {
            $columns['slug_hash'] = md5($columns['slug_path']);
        }
        return $columns;
    }
}
