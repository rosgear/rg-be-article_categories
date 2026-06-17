<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories\Model;

use Ge;
use Ge\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля записи категории материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class GridRow extends FormModel
{
    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName'  => '{{article_category}}',
            'primaryKey' => 'id',
            'fields'     => [
                ['id'],
                ['publish'] // опубликовать
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                if ($message['success']) {
                    if (isset($columns['publish'])) {
                        $publish = (int) $columns['publish'];
                        $message['message'] = $this->t('Article category - ' . ($publish > 0 ? 'published' : 'unpublished'));
                        $message['title']   = $this->t('Publication');
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
            });
    }
}
