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
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Data\Model\FormModel;

/**
 * Модель данных перемещения категории материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class Move extends FormModel
{
    /**
     * @var string Событие, возникшее после перемещении категории материала.
     */
    public const EVENT_AFTER_MOVE = 'afterMove';

    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\ArticleCategories\Module
     */
    public BaseModule $module;

    /**
     * Раздел (категория) куда переносится выбранная категория материала.
     * 
     * @var null|array
     */
    public $moveTo;

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
                ['name'], // название
                ['slug'], // ярлык
                [  // путь
                    'slug_path', 
                    'alias' => 'slugPath'
                ],
                [  // хэш пути
                    'slug_hash', 
                    'alias' => 'slugHash'
                ],
                // поля Nested Set
                ['ns_left'],
                ['ns_right'],
                ['ns_parent'],
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
            ->on(self::EVENT_AFTER_MOVE, function ($source, $destination, $result, $message) {
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type'])
                        ->cmdReloadTreeGrid($this->module->viewId('grid'));
            });
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // если выбран пункт меню "Переместить категорию"
            $moveTo = Ge::$app->request->post('moveTo');
            if ($moveTo) {
                /** @var \Ge\NestedSet\Nodes $nestedSet */
                $nestedSet = $this->module->getNestedSet();
                $this->moveTo = $nestedSet->getNode($moveTo);
                if ($this->moveTo === null) {
                    $this->setError($this->t('The category you selected does not exist'));
                    return false;
                }
                // проверка возможности перемещения категории
                if (!$nestedSet->canMove($this->attributes, $this->moveTo)) {
                    $this->setError($this->t('Unable to move the category to the section you selected'));
                    return false;
                }
            }
        }
        return $isValid;
    }

    /**
     * Перемещение и проверка категории материала.
     *
     * @param bool $useValidation Выполнять проверку полей категории материала (по умолчанию `false`).
     * 
     * @return bool Значение `false`, если была ошибка перемешения категории или ошибка 
     *     проверка полей.
     */
    public function move(bool $useValidation = false): bool
    {
        if ($useValidation && !$this->validate()) {
            return false;
        }
        return $this->moveProcess();
    }

    /**
     * Процесс перемещения категории материала.
     * 
     * @return bool Значение `false`, если была ошибка перемешения категории материала.
     */
    protected function moveProcess(): bool
    {
        /** @var \Ge\NestedSet\Nodes $nestedSet */
        $nestedSet = $this->module->getNestedSet();
        // замена всем потомкам URL-пути
        $oldSlugPath = $this->slugPath;
        $newSlugPath = $this->moveTo['slug_path'] . '/' . $this->slug;
        $nestedSet->replaceChildsColumn($this->attributes, 'slug_path', $newSlugPath . '/', $oldSlugPath . '/');

        // перемещение категории
        $this->result = $nestedSet->move($this->attributes, $this->moveTo);
        if ($this->result) {
            // обновление поля родителя (ns_parent)
            $this->{$nestedSet->parentColumn} = $this->moveTo[$nestedSet->idColumn];
            $this->slugPath = $newSlugPath;
            $this->slugHash = md5($newSlugPath);
            $this->save();
        }
        $this->afterMove($this->attributes, $this->moveTo, $this->result);
        return $this->result;
    }

    /**
     * Возвращает сообщение полученное при перемещении категории материала.
     *
     * @param bool $result Значение false, если была ошибка перемещения категории 
     *     материала.
     * 
     * @return array Сообщение имеет вид:
     * ```php
     *     [
     *         "success" => true,
     *         "message" => "Article category successfully moved",
     *         "title"   => "Moving",
     *         "type"    => "accept"
     *     ]
     * ```
     */
    public function moveMessage(bool $result): array
    {
        if ($result)
            $message = $this->t('Article category successfully moved');
        else
            $message = $this->t('Unable to move the category to the section you selected');
        return [
            'success'  => $result,
            'message'  => $message,
            'title'    => $this->t('Moving'),
            'type'     => $result ? 'accept' : 'error'
        ];
    }

    /**
     * Этот событие вызывается в конце перемещения категории материала.
     * 
     * @param array|null $source Переносимая категория.
     * @param array|null $destination Кому переносится категория.
     * @param bool $result Результат перемещения.
     * 
     * @return void
     */
    public function afterMove(array $source = null, array $destination = null, bool $result = false): void
    {
        $this->trigger(
            self::EVENT_AFTER_MOVE, 
            [
                'source'      => $source, 
                'destination' => $destination, 
                'result'      => $result, 
                'message'     => $this->moveMessage($result)
            ]
        );
    }
}
