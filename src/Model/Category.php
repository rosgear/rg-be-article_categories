<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru//framework/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories\Model;

use Ge\Site\Data\Model\ArticleCategory;

/**
 * Активная запись категории материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class Category extends ArticleCategory
{
    /**
     * Удаляет все записи.
     * 
     * @throws \Ge\Db\Adapter\Driver\Exception\CommandException Невозможно выполнить инструкцию SQL.
     */
    public function deleteAll()
    {
        $this->getDb()
            ->createCommand()
                ->truncateTable($this->tableName())
                ->execute();
    }
}
