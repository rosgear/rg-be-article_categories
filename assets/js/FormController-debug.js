/*!
 * Контроллер представления виджета формы.
 * Модуль "Материалы сайта".
 * Copyright 2015 RosGear. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://rosgear.ru/license/
 */

Ext.define('Rg.be.article_categories.FormController', {
    extend: 'Ge.view.form.PanelController',
    alias: 'controller.rg-be-article_categories-form',

    /**
     * Изменение значения флажка "только для робота Google".
     * @param {Ext.form.field.Checkbox} me
     * @param {Object} newValue
     * @param {Object} oldValue
     * @param {Object} eOpts
     */
     onChangeMain: function (me, newValue, oldValue, eOpts) {
        let types = Ext.getCmp(this.view.id + '__types');
        if (newValue)
            types.show();
        else
            types.hide();
    },
});