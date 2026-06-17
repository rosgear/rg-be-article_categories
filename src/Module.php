<?php
/**
 * Модуль веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories;

use Ge\NestedSet\Nodes;

/**
 * Модуль категорий материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories
 * @since 1.0
 */
class Module extends \Ge\Panel\Module\Module
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'rg.be.article_categories';

    /**
     * Модель Nested Set (вложенного множества).
     * 
     * @var Nodes
     */
    protected Nodes $nestedSet;

    /**
     * Возвращает модель Nested Set (вложенного множества).
     * 
     * @param null $dataManager
     * 
     * @return Nodes
     */
    public function getNestedSet($dataManager = null): Nodes
    {
        if (!isset($this->nestedSet)) {
            $this->nestedSet = new Nodes([
                'tableName'    => $dataManager ? $this->dataManager->tableName : '{{article_category}}',
                'parentColumn' => $dataManager ? $this->dataManager->parentKey : 'ns_parent'
            ]);
        }
        return $this->nestedSet;
    }
}
