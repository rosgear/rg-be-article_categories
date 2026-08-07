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
use Ge\Panel\Http\Response;
use Ge\Panel\Helper\ExtForm;
use Ge\Panel\Helper\ExtCombo;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер перемещения категории материала.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\ArticleCategories\Controller
 * @since 1.0
 */
class Move extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'Move';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 500;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->title = '#{move.title}';
        $window->titleTpl = '#{move.titleTpl}';
        $window->iconCls = 'g-icon-svg rg-acategories__icon-move-to';

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->resizable = false;
        $window->form->bodyPadding = 10;
        $window->form->defaults = [
            'labelAlign' => 'right',
            'labelWidth' => 120,
            'anchor'     => '100%',
        ];
        $window->form->items = [
            ExtCombo::trigger('#Move to', 'moveTo', 'categories', false)
        ];
        $window->form->router->route = Ge::alias('@match', '/move');
        $window->form->router->state = $window->form::STATE_CUSTOM;
        $window->form->router->rules = [
            'perform' => '{route}/perform/{id}',
            'data'    => '{route}/data/{id}'
        ];
        $window->form->buttons = ExtForm::buttons([
            'info',
            'save' => [
                'text'    => $this->t('Apply'),
                'handler' => 'onFormAction',
                'handlerArgs' => [
                    'routeRule' => 'perform',
                ]
            ],
            'cancel'
        ]);
        return $window;
    }

    /**
     * Действие "move" выполняет перемещение категории материала по указанному 
     * идентификатору.
     * 
     * @return Response
     */
    public function performAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Rg\Backend\ArticleCategories\Model\Move|null $model */
        $model = $this->getModel($this->defaultModel);
        if ($model === null) {
            $response
                ->meta->error(Ge::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

         /** @var \Rg\Backend\ArticleCategories\Model\Move|null $form*/
        $form = $model->get();
        if ($form === null) {
            $response
                ->meta->error(Ge::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        if ($this->useAppEvents) {
            Ge::$app->doEvent($this->makeAppEventName(), [$this, $form]);
        }

        // валидация атрибутов модели
        if (!$form->validate()) {
            $response
                ->meta->error(Ge::t(BACKEND, 'Error filling out form fields: {0}', [$form->getError()]));
            return $response;
        }

        // перемещение категории
        if (!$form->move()) {
            $response
                ->meta->error($form->hasErrors() ? $form->getError() : Ge::t(BACKEND, 'Could not save data'));
            return $response;
        }
        return $response;
    }
}
