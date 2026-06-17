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
use Ge\Helper\Url;
use Ge\Panel\Http\Response;
use Ge\Panel\Helper\ExtCombo;
use Ge\Panel\Helper\HtmlGrid;
use Ge\Panel\Widget\TabTreeGrid;
use Ge\Panel\Helper\ExtGridTree as ExtGrid;
use Ge\Panel\Helper\HtmlNavigator as HtmlNav;
use Ge\Panel\Controller\TreeGridController;

/**
 * Контроллер списка категорий материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Controller
 * @since 1.0
 */
class Grid extends TreeGridController
{
    /**
     * Действие "move" выполняет перемещение категории материала.
     * 
     * @return Response
     */
    public function moveAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Rg\Backend\ArticleCategory\Model\Move $model модель данных */
        $model = $this->getModel('Move');
        if ($model === false) {
            $response
                ->meta->error(Ge::t('app', 'Could not defined data model "{0}"', [$model]));
            return $response;
        }
        /** @var \Rg\Backend\ArticleCategory\Model\Move $form запись по идентификатору запроса */
        $form = $model->get();
        if ($form === null) {
            $response
                ->meta->error(Ge::t(BACKEND, 'No data to perform action'));
            return $response;
        }
        // валидация атрибутов модели
        if (!$form->validate()) {
            $response
                ->meta->error(Ge::t(BACKEND, 'Error filling out form fields: {0}', [$form->getError()]));
            return $response;
        }
        // перемещение категории
        if (!$form->move()) {
            $response->meta->error($form->hasErrors() ? $form->getError() : Ge::t(BACKEND, 'Could not save data'));
            return $response;
        }
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabTreeGrid
    {
       /** @var TabTreeGrid $tab Сетка данных в виде дерева (Ge.view.grid.Tree GeJS) */
        $tab = parent::createWidget();

        // столбцы (Ge.view.grid.Tree.columns GeJS)
        $tab->treeGrid->columns = [
            ExtGrid::columnAction(),
            [
                'text'      => '№',
                'tooltip'   => '#Index number',
                'dataIndex' => 'asIndex',
                'filter'    => ['type' => 'numeric'],
                'hidden'    => true,
                'width'     => 70
            ],
            [
                'xtype'     => 'treecolumn',
                'text'      => ExtGrid::columnInfoIcon($this->t('Name')),
                'cellTip'   => HtmlGrid::tags([
                    HtmlGrid::header('{name}'),
                    HtmlGrid::fieldLabel($this->t('Index number'), '{index}'),
                    HtmlGrid::fieldLabel($this->t('Slug'), '{slug}'),
                    HtmlGrid::fieldLabel($this->t('Language'), '{language}'),
                    HtmlGrid::fieldLabel($this->t('Number of subcategories'), '{count}'),
                    HtmlGrid::fieldLabel(
                        $this->t('Published'),
                        HtmlGrid::tplChecked('publish')
                    )
                ]),
                'dataIndex' => 'name',
                'filter'    => ['type' => 'string'],
                'width'     => 300
            ],
            [
                'text'      => '#Slug',
                'tooltip'   => '#The Slug is a version of a name, a unique part of a URL. These are all lowercase letters and only Latin letters, numbers and hyphens.',
                'cellTip'   => '{slug}',
                'dataIndex' => 'slug',
                'filter'    => ['type' => 'string'],
                'width'     => 180
            ],
            [
                'text'      => '#Path',
                'tooltip'   => '#The path is the unique part of the URL. Consists of a category slug and subcategory slug (if any). Created automatically.',
                'cellTip'   => '{slugPath}',
                'dataIndex' => 'slugPath',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'width'     => 180
            ],
            [
                'text'      => '#Language',
                'dataIndex' => 'language',
                'tooltip'   => '#The language in which the category will be accessed on the site',
                'cellTip'   => '{language}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'width'     => 150
            ],
            [
                'xtype'    => 'templatecolumn',
                'sortable' => false,
                'width'    => 45,
                'align'    => 'center',
                'tpl'      => HtmlGrid::a(
                    '', 
                    '{url}',
                    [
                        'title' => $this->t('View category'),
                        'class' => 'g-icon g-icon-svg g-icon_size_14 g-icon-m_link g-icon-m_color_default g-icon-m_is-hover',
                        'target' => '_blank'
                    ]
                )
            ],
            [
                'text'      => ExtGrid::columnIcon('g-icon-m_nodes', 'svg'),
                'tooltip'   => '#Number of subcategories',
                'align'     => 'center',
                'dataIndex' => 'count',
                'sortable'  => false,
                'width'     => 60
            ],
            [
                'text'      => ExtGrid::columnIcon('g-icon-m_visible', 'svg'),
                'xtype'     => 'g-gridcolumn-switch',
                'tooltip'   => '#Category is available and published',
                'selector'  => 'treepanel',
                'filter'    => ['type' => 'boolean'],
                'dataIndex' => 'publish'
            ]
        ];

        // панель инструментов (Ge.view.grid.Tree.tbar GeJS)
        $tab->treeGrid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Добавить"
                        'add' => [
                            'iconCls' => 'g-icon-svg rg-acategories__icon-add',
                            'caching' => false
                        ],
                        // инструмент "Удалить"
                        'delete' => [
                            'iconCls' => 'g-icon-svg rg-acategories__icon-delete',
                        ],
                        'cleanup',
                        '-',
                        'edit',
                        'select',
                        '-',
                        'refresh'
                    ]
                ],
                'columns',
                'search' => [
                    'items' => [
                        'help',
                        'search',
                        'filter' => [
                            'form' => [
                                'cls'      => 'g-popupform-filter',
                                'width'    => 400,
                                'height'   => 'auto',
                                'action'   => Url::toMatch('grid/filter'),
                                'defaults' => ['labelWidth' => 130],
                                'items'    => [
                                    ExtCombo::languages('#Language', 'language'),
                                    ExtGrid::fieldsetAudit()
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        ];

        // контекстное меню записи (Ge.view.grid.Tree.popupMenu GeJS)
        $tab->treeGrid->popupMenu = [
            'cls'        => 'g-gridcolumn-popupmenu',
            'titleAlign' => 'center',
            'items'      => [
                [
                    'text'        => '#Edit category',
                    'iconCls'     => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => Ge::alias('@match', '/form/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ],
                '-',
                [
                    'text'        => '#Add category to section',
                    'iconCls'     => 'g-icon-svg rg-acategories__icon-add-to g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => Ge::alias('@match', '/form/view/?appendTo={id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ],
                [
                    'text'        => '#Move category',
                    'iconCls'     => 'g-icon-svg rg-acategories__icon-move-to g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => Ge::alias('@match', '/move/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ]
            ]
        ];

        // поле аудита записи
        $tab->treeGrid->logField = 'name';
        // количество строке в сетке
        $tab->treeGrid->store->pageSize = 50;
        // класс CSS применяемый к элементу body сетки
        $tab->treeGrid->bodyCls = 'g-grid_background';
        // выбор только одной строки
        $tab->treeGrid->multiSelect = false;
        $tab->treeGrid->nodesDraggable = true;
        $tab->treeGrid->nodesDropConfig = [
            'confirm'      => true,
            'confirmTitle' => '#{move.title}',
            'confirmMsg'   => '#You agree to move "{0}" to "{1}"?',
            'dropNodeName' => 'name'
        ];
        $tab->treeGrid->nodesDragConfig = [
            'displayField' => 'name',
            'appendOnly'   => true
        ];
        $tab->treeGrid->router->rules['move'] = '{route}/move/{id}';

        // панель навигации (Ge.view.navigator.Info GeJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::header('{name}'),
            ['fieldset',
                [
                    HtmlNav::fieldLabel($this->t('Index number'), '{index}'),
                    HtmlNav::fieldLabel($this->t('Slug'), '{slug}'),
                    HtmlNav::fieldLabel($this->t('Path'), '{slugPath}'),
                    HtmlNav::fieldLabel($this->t('Language'), '{language}'),
                    HtmlNav::fieldLabel(
                        ExtGrid::columnIcon('g-icon-m_nodes', 'svg') . ' ' . $this->t('Number of subcategories'),
                        '{count}'
                    ),
                    HtmlNav::fieldLabel(
                        ExtGrid::columnIcon('g-icon-m_visible', 'svg') . ' ' . $this->t('Published'),
                        HtmlNav::tplChecked('publish')
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Edit category'),
                        ['route' => Ge::alias('@match', '/form/view/{id}'), 'long' => true],
                        ['title' => $this->t('Edit category')]
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Add category to section'),
                        ['route' => Ge::alias('@match', '/form/view/?appendTo={id}'), 'long' => true],
                        ['title' => $this->t('Add category to section')]
                    )
                ]
            ]
        ]);

        $tab
            ->addCss(GE_DEBUG ? '/grid.css' : '/grid.min.css')
            ->addRequire('Ge.view.grid.column.Switch');
        return $tab;
    }
}
