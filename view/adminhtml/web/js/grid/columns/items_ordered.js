/**
 * Illia Neiman
 *
 * NOTICE OF LICENSE
 *
 * According to LICENCE file you are not allowed to copy, use or recreate this file in any ways.
 * Specially for eCommerce usage.
 *
 * @category Ineiman
 * @package SalesGrid
 * @copyright Copyright (c) 2021-current Ineiman (https://github.com/illyaneiman)
 * @email kg.illya.ney@gmail.com
 */

define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'uiCollection',
    'ko',
    'uiRegistry'
], function (_, utils, layout, Collection, ko, registry) {
    'use strict';

    return Collection.extend({
        defaults: {
            headerTmpl: 'ui/grid/columns/text',
            bodyTmpl: 'Ineiman_SalesGrid/ui/grid/cells/items_ordered',
            disableAction: true,
            controlVisibility: true,
            sortable: true,
            sorting: false,
            visible: true,
            draggable: true,
            listingFiltersPath: 'ineiman_salesgrid_listing.ineiman_salesgrid_listing.listing_top.listing_filters',
            columns: {
                base: {
                    parent: '${ $.name }',
                    component: 'Magento_Ui/js/grid/columns/column',
                    bodyTmpl: 'Ineiman_SalesGrid/ui/grid/cells/text',
                    headerTmpl: 'Ineiman_SalesGrid/ui/grid/columns/text',
                    filter: 'text',
                    defaults: {
                        draggable: false,
                        sortable: false
                    },
                    initObservable: function () {
                        this._super()
                            .track([
                                'visible',
                                'sorting',
                                'disableAction',
                                'subVisible',
                                'label'
                            ])
                            .observe([
                                'dragging'
                            ]);

                        return this;
                    }
                }
            },
            imports: {
                productCols: '${ $.columnsControlsProvider }:productCols'
            },
            listens: {
                productCols: 'updateProductCols',
                elems: 'updateFilters'
            }
        },

        initElement: function (el) {
            el.track(['label', 'subVisible']);
        },

        initialize: function () {
            this._super();

            registry.async(this.listingFiltersPath)(function (listingFilters) {
                this.listingFilters = listingFilters;
                this.updateFilters();
            }.bind(this));

            return this;
        },

        updateFilters: function () {
            if (this.listingFilters) {
                _.each(this.elems(), function (column) {
                    if (column.filter) {
                        column.visible = column.subVisible;
                        column.label = column.gridjunior_label();
                        this.listingFilters.addFilter(column);
                    }
                }.bind(this));
            }
        },

        updateProductCols: function () {
            _.each(this.getVisibleCols(), function (col) {
                let config = utils.extend({}, this.columns.base, {
                    name: col.index,
                    subVisible: col.visible,
                    visible: col.visible,
                    gridjunior_label: ko.observable(col.gridjunior_label),
                    filter: col.filter
                });

                let component = utils.template(config, {
                });

                layout([component]);
            }.bind(this));

            _.each(this.elems(), function (elem) {
                _.each(this.productCols, function (col) {
                    if (elem.index === col.index) {
                        elem.visible = col.visible;
                        elem.subVisible = col.visible;

                        if (ko.isObservable(elem.gridjunior_label)) {
                            elem.gridjunior_label(col.gridjunior_label);
                        }
                    }
                });
            }.bind(this));
        },

        initObservable: function () {
            this._super()
                .track([
                    'visible',
                    'sorting',
                    'disableAction',
                    'productCols'
                ])
                .observe([
                    'dragging'
                ]);

            return this;
        },

        initFieldClass: function () {
            _.extend(this.fieldClass, {
                _dragging: this.dragging
            });

            return this;
        },

        getVisibleCols: function () {
            return _.filter(this.productCols, function (el) {
                return el.visible === true;
            });
        },

        getColumns: function () {
            return this.elems.filter('subVisible');
        },

        getItems: function (record) {
            let orderData = record[this.index];

            return _.map(orderData);
        },

        getFieldClass: function () {},

        getHeader: function () {
            return this.headerTmpl;
        },

        getBody: function () {
            return this.bodyTmpl;
        },

        sort: function (enable) {},

        getFieldHandler: function () {}
    });
});
