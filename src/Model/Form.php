<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories\Model;

use URLify;
use Ge;
use Ge\Db\Sql\Expression;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Data\Model\FormModel;
use Ge\Site\Data\Model\Article;

/**
 * Модель данных профиля категории материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class Form extends FormModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\ArticleCategories\Module
     */
    public BaseModule $module;

    /**
     * Раздел в который добавляется категория.
     * 
     * @var array|null
     */
    public ?array $appendTo = null;

    /**
     * Главная страница материала категории.
     * 
     * @var Article|null
     */
    protected ?Article $article = null;

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
                ['id'],
                [ // порядковый номер
                    'index', 
                    'label' => 'Index'
                ],
                [ // название
                    'name', 
                    'label' => 'Name'
                ],
                ['publish'], // опубликовать
                [ // язык
                    'language_id', 
                    'alias' => 'language', 
                    'label' => 'Language'
                ],
                [ // ярлык
                    'slug', 
                    'label' => 'Slug'
                ],
                [ // путь
                    'slug_path', 
                    'alias' => 'slugPath'
                ],
                [ // хэш пути
                    'slug_hash', 
                    'alias' => 'slugHash'
                ],
                // поля Nested Set
                ['ns_parent', 'alias' => 'nsParent'],
                ['ns_left'],
                ['ns_right'],
                ['addMainPage'], // добавить материал в виде главной страницы
                ['typeId'] // тип материала
            ],
            'resetIncrements' => ['{{article_category}}'],
            // правила форматирования полей
            'formatterRules' => [
                [['name'], 'safe'],
                [['publish', 'addMainPage'], 'logic']
            ],
            // правила валидации полей
            'validationRules' => [
                [['index', 'name'], 'notEmpty'],
                [ // название
                    'name',
                    'between',
                    'max' => 255, 'type' => 'string'
                ],
                // порядковый номер
                [
                    'index', 
                    'between',
                    'min' => 1, 'max' => PHP_INT_MAX
                ]
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
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type'])
                        ->cmdReloadTreeGrid($this->module->viewId('grid'));
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                /** @var \Rg\Backend\ArticleCategories\Helper\Helper $helper */
                $helper = $this->module->getHelper();
                // попытка удалить главную страницу (статью) категории
                if ($message['success'] && ($helper->deleteArticles([$this->node]) !== true)) {
                    $message['success'] = false;
                    $message['type']    = 'error';
                    $message['message'] = $this->t('Categories have not been deleted, because there was an error in updating articles (records) of the site');
                }
                // попытка удалить категорию
                if ($message['success']) {
                    /** @var \Ge\NestedSet\Nodes $nestedSet */
                    $nestedSet = $this->module->getNestedSet();
                    if ($nestedSet->delete($this->node) == 0) {
                         $message['success'] = false;
                         $message['type']    = 'error';
                         $message['message'] = $this->t('Error while deleting selected categories');
                    }
                }
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type'])
                        ->cmdReloadTreeGrid($this->module->viewId('grid'));
            });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoad(array &$data): void
    {
        // обязательно указываем, чтобы имя атрибута было в методе `load`
        // т.к. необходим вызов метода unSlugPath, unSlugHash
        $data['slugPath'] = '';
        $data['slugHash'] = '';
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): false|int
    {
        $result = false;
        if ($this->beforeDelete()) {
            /** @var \Ge\NestedSet\Nodes $nestedSet */
            $nestedSet = $this->module->getNestedSet();
            if ($this->node = $nestedSet->getNode($this->getIdentifier())) {
                // если есть поле "_lock" в таблице
                if ($this->dataManager->lockRows && isset($this->node[$this->dataManager::AR_LOCK])) {
                    $result = !($this->node[$this->dataManager::AR_LOCK] == 1);
                } else
                    $result = true;
                if ($result) {
                    $result = $nestedSet->getChildCount($this->node) + 1;
                }
            }
            $this->afterDelete($result);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeUpdate(array &$columns): void
    {
        // если значение ярлыка изменилось
        if ($this->isDirtyAttribute('slug')) {
            // если есть предыдущие значение
            if ($oldSlugPath = $this->getOldAttribute('slugPath')) {
                /** @var \Ge\NestedSet\Nodes $nodes */
                $nodes = $this->module->getNestedSet();
                // если есть потомки
                if ($nodes->getChildCount($this->attributes) > 0) {
                    // обновление всем потомкам ярлыков
                    $nodes->replaceChildsColumn($this->attributes, 'slug_path', $this->slugPath . '/', $oldSlugPath . '/');
                    // обновление всем потомкам хэша
                    $nodes->updateChilds(
                        $this->attributes, 
                        [
                            'slug_hash' => new Expression('MD5(slug_path)')
                        ]
                    );
                }
            }
        }
        unset($columns['typeId'], $columns['addMainPage']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeInsert(array &$columns): void
    {
        // если добавление материала в виде главной страницы категории
        if ($this->addMainPage > 0) {
            $this->article = new Article();
            $this->article->typeId = $this->typeId;
        }
        unset($columns['typeId'], $columns['addMainPage']);
    }

    /**
     * {@inheritdoc}
     */
    public function insertRecord(array $columns): int|string
    {
        /** @var int|false $id Идентификатор новой категории */
        $id = $this->module->getNestedSet()->add($columns, $this->appendTo ? $this->appendTo['id'] : null);
        // если указано создание главной страницы категории
        if ($id && $this->article) {
            $this->article->publish = 1;
            $this->article->publishOnMain = 1;
            $this->article->publishInCategories = 1;
            $this->article->publishDate = Ge::$app->formatter->toDateTime('now', 'Y-m-d H:i:s', false, Ge::$app->dataTimeZone);
            $this->article->categoryId = $id;
            $this->article->header = $columns['name'];
            $this->article->language = $columns['language_id'];
            $this->article->setSlugType(3);

            // TODO: нет проверки на добавление
            $this->article->save();
        }
        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        $isValid = parent::afterValidate($isValid);
        if ($isValid) {
            if ($this->addMainPage) {
                // необходимо указать тип материала
                if (empty($this->typeId)) {
                    $this->addError(
                        $this->errorFormatMsg(
                            Ge::t('app', "Value is required and can't be empty"), 
                            $this->module->t('Article type')
                        )
                    );
                    return false;
                }
            }
        }
        return $isValid;
    }


    /**
     * Возращает значение для сохранения в поле "slug".
     * 
     * @return string
     */
    public function unSlug(): ?string
    {
        $value = $this->slug;

        $value = $value ?: $this->name;
        $value = $value ? URLify::filter($value, 255, '', true) : null;
        return $this->slug = $value;
    }

    /**
     * Возращает значение для сохранения в поле "slug_path".
     * 
     * @return string
     */
    public function unSlugPath(): ?string
    {
        // если добавление записи
        if ($this->isNewRecord()) {
            // если категория добавляется в указанный раздел
            if ($this->appendTo) {
                return $this->slugPath = $this->appendTo['slug_path'] . '/' . $this->slug;
            }
        // если изменение записи
        } else {
            // если изменился ярлык
            if ($this->isDirtyAttribute('slug')) {
                // если категория находится в разделе
                if ($this->nsParent) {
                    /** @var array|null $parent */
                    $parent = $this->module->getNestedSet()->getNode($this->nsParent);
                    if ($parent) {
                        return $this->slugPath = $parent['slug_path'] . '/' . $this->slug;
                    }
                }
            }
        }
        return $this->slugPath = $this->slug;
    }

    /**
     * Возращает значение для сохранения в поле "slug_hash".
     * 
     * @return string
     */
    public function unSlugHash(): ?string
    {
        return $this->slugPath ? md5($this->slugPath) : null;
    }

    /**
     * Устанавливает значение атрибуту "typeId".
     * 
     * @param null|string|int $value
     * 
     * @return void
     */
    public function setTypeId($value): void
    {
        $value = (int) $value;
        $this->attributes['typeId'] = $value === 0 ? null : $value;
    }

    /**
     * Устанавливает значение атрибуту "language".
     * 
     * @param null|string|int $value
     * 
     * @return void
     */
    public function setLanguage($value): void
    {
        $value = (int) $value;
        $this->attributes['language'] = $value === 0 ? null : $value;
    }

    /**
     * Возвращает значение атрибута "language" для элемента интерфейса формы.
     * 
     * @param null|string|int $value
     * 
     * @return array
     */
    public function outLanguage($value): array
    {
        $language = $value ? Ge::$app->language->available->getBy($value, 'code') : null;
        if ($language) {
            return [
                'type'  => 'combobox', 
                'value' => $language['code'],
                'text'  => $language['shortName'] . ' (' . $language['tag'] . ')'
            ];
        }
        return [
            'type'  => 'combobox',
            'value' => 0,
            'text'  => Ge::t(BACKEND, '[None]')
        ];       
    }
}
