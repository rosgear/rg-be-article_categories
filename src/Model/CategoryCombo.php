<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories\Model;

use Ge\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка категорий.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class CategoryCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{article_category}}',
            'primaryKey' => 'id',
            'order'      => ['ns_left' => 'asc'],
            'searchBy'   => 'name',
            'fields' => [
                ['name']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterFetchRow(array $row, array &$rows): void
    {
        // количество потомков
        $count = ($row['ns_right'] - $row['ns_left'] - 1) / 2;
        $rows[] = [$row[$this->dataManager->primaryKey], $row[$this->dataManager->searchBy], $count ? 'folder' : 'leaf'];
    }
}
