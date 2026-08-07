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
use Ge\Helper\Url;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Data\Model\TreeGridModel;

/**
 * Модель данных сетки категорий материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class Grid extends TreeGridModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\ArticleCategories\Module
     */
    public BaseModule $module;

    /**
     * @var null|array
     */
    protected ?array $selectedNodes;

    /**
     * Установленные языки.
     *
     * @var array<int, array>
     */
    protected array $languages = [];

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'useAudit'   => true,
            'tableName'  => '{{article_category}}',
            'primaryKey' => 'id',
            'parentKey'  => 'ns_parent',
            'fields'     => [
                ['index', 'alias' => 'asIndex'], // порядковый номер
                ['name'], // название
                ['publish'], // опубликовать
                ['language_id', 'alias' => 'language'], // язык
                ['url'], // URL-адрес категории
                ['slug'], // ярлык (слаг)
                [ // путь
                    'slug_path', 
                    'alias' => 'slugPath'
                ],
                // поля Nested Set
                ['ns_left', 'alias' => 'nsLeft'],
                ['ns_right', 'alias' => 'nsRight']
            ],
            'order' => [
                'ns_left' => 'ASC'
            ],
            'resetIncrements' => ['{{article_category}}'],
            'filter' => [
                'language' => ['operator' => '=']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // все доступные языки
        $this->languages = Ge::$app->language->available->getAllBy('code');
        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                /** @var \Rg\Backend\ArticleCategories\Helper\Helper $helper */
                $helper = $this->module->getHelper();
                // попытка удалить главные страницы (статьи) категорий
                if ($message['success'] && ($helper->deleteArticles($this->selectedNodes) !== true)) {
                    $message['success'] = false;
                    $message['type']    = 'error';
                    $message['message'] = $this->t('Categories have not been deleted, because there was an error in updating articles (records) of the site');
                }
                // попытка удалить выделенные категории
                if ($message['success'] && $someRecords) {
                    /** @var \Ge\NestedSet\Nodes $nestedSet */
                    $nestedSet = $this->module->getNestedSet();
                    foreach ($this->selectedNodes as $node) {
                        if ($nestedSet->delete($node) == 0) {
                             $message['success'] = false;
                             $message['type']    = 'error';
                             $message['message'] = $this->t('Error while deleting selected categories');
                             break;
                        }
                    }
                }
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']) // всплывающие сообщение
                        ->cmdReloadTreeGrid($this->module->viewId('grid')); // обновить дерево
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                $this->response()
                    ->meta
                        ->cmdReloadTreeGrid($this->module->viewId('grid'), 'root'); // обновить дерево
            });
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRow(array $row): array
    {
        // язык
        $languageSlug = null;
        if ($row['language_id']) {
            $language = $this->languages[(int) $row['language_id']] ?? null;
            if ($language) {
                $languageSlug = $language['slug'];
                $row['language_id'] = $language['shortName'] . ' (' . $language['slug'] . ')';
            }
        }
        // URL-адрес
        $row['url'] = Url::to([$row['slug_path'], 'langSlug' => $languageSlug]);
        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRow(array &$row): void
    {
        // заголовок контекстного меню записи
        $row['popupMenuTitle'] = $row['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function fetchChildCount(array $row): int
    {
        $range = ($row['nsRight'] ?? 0) - ($row['nsLeft'] ?? 0);
        if ($range)
            return ($range - 1) / 2;
        else
            return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectedCount(): int
    {
        $count = 0;
        if ($this->selectedNodes) {
            /** @var \Ge\NestedSet\Nodes $nestedSet */
            $nestedSet = $this->module->getNestedSet();
            foreach ($this->selectedNodes as $node) {
                $count += $nestedSet->getChildCount($node);
            }
            $count += 1; // т.к. включает свой выделенный элемент
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): false|int
    {
        $result = false;
        if ($this->beforeDelete()) {
            // если в запросе указан идентификатор
            $identifier = $this->getIdentifier();
            if ($identifier) {
                /** @var \Ge\NestedSet\Nodes $nestedSet */
                $nestedSet = $this->module->getNestedSet();
                if ($this->selectedNodes = $nestedSet->getNode($identifier)) {
                    $node = $this->selectedNodes[0];
                    // если есть поле "_lock" в таблице
                    if ($this->dataManager->lockRows && isset($node[$this->dataManager::AR_LOCK])) {
                        $result = !($node[$this->dataManager::AR_LOCK] == 1);
                    } else
                        $result = true;
                    if ($result) {
                        $result = $nestedSet->getChildCount($node) + 1;
                    }
                }
            }
            $this->afterDelete(true, $result);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll(string $tableName = null): false|int
    {
        $result = false;
        if ($this->beforeDelete(false)) {
            /** @var \Ge\NestedSet\Nodes $nestedSet */
            $nestedSet = $this->module->getNestedSet();
            $result = $nestedSet->clean();
            $this->afterDelete(false, $result);
        }
        return $result;
    }
}
