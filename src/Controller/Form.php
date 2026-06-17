<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\ArticleCategories\Controller;

use Ge;
use Ge\Stdlib\BaseObject;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Helper\ExtCombo;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер формы категории материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Controller
 * @since 1.0
 */
class Form extends FormController
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
    protected ?array $appendTo = null;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_BEFORE_ACTION, function ($controller, $action, &$result) {
                switch ($action) {
                    case 'add':
                        $appendTo = Ge::$app->request->getPost('appendTo', 0, 'int');
                        break;

                    case 'view':
                        $appendTo = Ge::$app->request->getQuery('appendTo', 0, 'int');
                        break;
                }

                if (isset($appendTo) && $appendTo > 0) {
                    $this->appendTo = $this->module->getNestedSet()->getNode($appendTo);

                    // проверяем, существует ли указанный раздел (родитель)
                    if ($this->appendTo === null) {
                        $this->getResponse()
                            ->meta->error($this->t('The category you selected does not exist'));
                        $result = false;
                        return;
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(string $name = null, array $config = []): ?BaseObject
    {
        $config['appendTo'] = $this->appendTo;

        return parent::getModel($name, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 580;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->resizable = false;
        if ($this->appendTo) {
            $window->title = $this->module->t('{appendTo.title}', ['name' => $this->appendTo['name']]);
            $window->iconCls = 'g-icon-svg rg-acategories__icon-add-to';
        }

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->makeViewID(); // для того, чтобы сразу использовать `$window->form->id`
        $window->form->controller = 'rg-be-article_categories-form';
        $window->form->items = [
            [
                'xtype'    => 'container',
                'style'    => 'padding: 5px',
                'layout'   => 'anchor',
                'defaults' => [
                    'labelWidth' => 115,
                    'labelAlign' => 'right'
                ],
                'items' => [
                    [
                        'xtype'      => 'spinnerfield',
                        'minValue'   => '1',
                        'emptyText'  => '1',
                        'width'      => 190,
                        'name'       => 'index',
                        'fieldLabel' => '#Index',
                        'tooltip'    => '#The index number is used to order the categories in the lists'
                    ],
                    ExtCombo::languages(
                        '#Language', 
                        'language', 
                        true, 
                        [
                            'tooltip' => '#The language in which the category will be accessed on the site',
                            'width'   => 300
                        ]
                    ),
                    [
                        'xtype'      => 'textfield',
                        'anchor'     => '100%',
                        'maxLength'  => 255,
                        'fieldLabel' => '#Name',
                        'name'       => 'name',
                        'allowBlank' => false
                    ],
                    [
                        'xtype'      => 'textfield',
                        'anchor'     => '100%',
                        'maxLength'  => 255,
                        'fieldLabel' => '#Slug',
                        'tooltip'    => '#The Slug is a version of a name, a unique part of a URL. These are all lowercase letters and only Latin letters, numbers and hyphens.',
                        'name'       => 'slug',
                        'allowBlank' => true
                    ],
                    $window->form->isInsertState() ? 
                    ExtCombo::trigger(
                        '#Article type', 
                        'typeId', 
                        'types', 
                        false,
                        'references/article-types/trigger/combo',
                        [
                            'id'      => $window->form->id . '__types',
                            'tooltip' => '#Type of added material for the main page of the category',
                            'hidden'  => true
                        ]
                    ) : null,
                    $window->form->isInsertState() ? 
                    [
                        'ui'         => 'switch',
                        'xtype'      => 'checkbox',
                        'fieldLabel' => '#Main page',
                        'boxLabel'   => '#creating a main page for a category',
                        'name'       => 'addMainPage',
                        'checked'    => false,
                        'inputValue' => 1,
                        'listeners'  => ['change' => 'onChangeMain']
                    ] : null,
                    [
                        'ui'         => 'switch',
                        'xtype'      => 'checkbox',
                        'fieldLabel' => '#Publish',
                        'name'       => 'publish',
                        'inputValue' => 1,
                        'checked'    => true
                    ],
                    [
                        'xtype' => 'hidden',
                        'name'  => 'appendTo',
                        'value' => $this->appendTo ? $this->appendTo['id'] : 0
                    ]
                ]
            ],
            $window->form->isInsertState() ? 
            [
                'xtype' => 'label',
                'id'    => 'acategory-comment',
                'ui'    => 'note',
                'text'  => '#to display the main page of a category, you need to create article (record) with the type "Main page" or set the flag "Main page"'
            ] : null
        ];
        $window
            ->setNamespaceJS('Rg.be.article_categories')
            ->addRequire('Rg.be.article_categories.FormController' . (GE_DEBUG ? '-debug' : ''));
        return $window;
    }
}
