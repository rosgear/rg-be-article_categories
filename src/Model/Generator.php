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
use URLify;
use Ge\NestedSet\Nodes;
use Ge\Data\Model\DataModel;
use Ge\Mvc\Module\BaseModule;
use Ge\Site\Data\Model\Article;

/**
 * Генератор категорий материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Model
 * @since 1.0
 */
class Generator extends DataModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\ArticleCategories\Module
     */
    public BaseModule $module;

    /**
     * Модель Nested Set (вложенного множества).
     * 
     * @var Nodes
     */
    protected Nodes $nestedSet;

    /**
     * Опции генератора.
     * 
     * @var array
     */
    protected array $options = [];

    /**
     * Максимальное количество категорий статей.
     * 
     * @var int
     */
    public int $maxCategories = 3;

    /**
     * Максимальное количество подкатегорий статей.
     * 
     * @var int
     */
    public int $maxSubCategories = 3;

    /**
     * Максимальное количество статей в категории.
     * 
     * @var int
     */
    public int $maxArticleInCategory = 3;

    /**
     * Добавление главной страницы (статьи) категории.
     * 
     * @var bool
     */
    public bool $mainArticleInCategory = true;

    /**
     * Шаблон имени категори статьи.
     * 
     * @var string
     */
    public string $nameTemplate = 'Category %s';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->nestedSet = $this->module->getNestedSet();
    }

    /**
     * Устанавливает опции генератору.
     * 
     * @param array $options Опции генератора.
     * 
     * @return $this
     */
    public function setOptions(array $options): static
    {
        $this->options = $options;
        Ge::configure($this, $options, false);
        return $this;
    }

    /**
     * Возвращает опции генератора.
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Удаляет все статьи.
     * 
     * @return $this
     */
    protected function articleClean(): static
    {
        $this->deleteRecord([], '{{article}}');
        $this->resetIncrement(1, '{{article}}');
        return $this;
    }

    /**
     * Добавляет категории статей согласно установленным опциям генератора.
     * 
     * @return void
     */
    public function generate(): void
    {
        // удаление всех категорий
        $this->nestedSet->clean();
        $this->articleClean();
        /** @var \Rg\Backend\ArticleCategories\Model\Article $article статья категории */
        $article = $this->module->getModel('Article');

        for ($i = 1; $i < $this->maxCategories + 1; $i++) {
            $parentName = sprintf($this->nameTemplate, $i);
            $parentSlug = URLify::filter($parentName, 255, '', true);
            $parent = $this->nestedSet->add([
                'name'      => $parentName,
                'slug'      => $parentSlug,
                'slug_path' => $parentSlug,
                'slug_hash' => md5($parentSlug)
            ]);
            // создание главной страницы (статьи) категории
            $article->reset();
            $article->header = sprintf('Main for Category %s', $i);
            $article->urlPath = null;
            $article->category = $parent;
            $article->type = Article::SLUG_HOME;
            $article->save();
            // если указано количество подкатегорий
            if ($this->maxSubCategories) {
                for ($j = 1; $j < $this->maxSubCategories + 1; $j++) {
                    $childName = sprintf($this->nameTemplate, $i . '-' . $j);
                    $childSlug = URLify::filter($childName, 255, '', true);
                    $child = $this->nestedSet->add([
                        'name'      => $childName,
                        'slug'      => $childSlug,
                        'slug_path' => $parentSlug . '/' . $childSlug,
                        'slug_hash' => md5($parentSlug . '/' . $childSlug)
                    ], $parent);
                    // создание статей категории
                    $article->reset();
                    $article->header = sprintf('Article category %s', $i . '-' . $j);
                    $article->category = $child;
                    $article->urlPath = URLify::filter($article->header, 255, '', true);
                    $article->type = Article::SLUG_DYNAMIC;
                    $article->save();
                }
            }
        }
    }
}
