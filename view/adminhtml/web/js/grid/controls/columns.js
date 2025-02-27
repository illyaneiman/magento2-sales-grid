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
    'ko',
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Ui/js/grid/controls/columns',
    'uiRegistry'
], function (ko, _, utils, layout, uiColumns, registry) {
    'use strict';

    return uiColumns.extend({
        defaults: {
            selectedTab: 'general',
            template: 'Ineiman_SalesGrid/ui/grid/controls/columns',
            _tabs: [],
            _productCols: [],
            imports: {
                addTabs: '${ $.name }:tabsData',
                addProductColsData: '${ $.name }:productColsData',
                addDefaultColumnsData: '${ $.columnsProvider }:elems'
            },
            clientConfig: {
                component: 'Magento_Ui/js/grid/editing/client',
                name: '${ $.name }_client'
            },
            listens: {
                '${ $.storageConfig.provider }:activeView': 'activeView'
            },
            modules: {
                client: '${ $.clientConfig.name }',
                source: '${ $.provider }'
            }
        },

        activeView: function (view) {
            _.each(this.productCols(), function (el) {
                if (!_.isUndefined(view.data.columns[el.index])) {
                    if (view.data.columns[el.index].gridjunior_title !== undefined) {
                        el.gridjunior_title = view.data.columns[el.index].gridjunior_title;
                    }

                    el.visible = view.data.columns[el.index].visible;
                }
            });
            this.productCols(this.productCols());
            this.showItemsOrderedColumn();
        },

        initialize: function () {
            _.bindAll(this, 'reloadGridData');

            this._super();

            layout([this.clientConfig]);

            return this;
        },

        initObservable: function () {
            this._super()
                .track(['selectedTab'])
                .observe({
                    tabs: [],
                    productCols: []
                });

            return this;
        },

        addTabs: function (tabs) {
            _.map(tabs, function (value, key) {
                return utils.insert({
                    key: key,
                    value: value,
                    _parent: this,
                    visible: this.isVisibleTab
                }, this._tabs);
            }.bind(this));

            this._tabs = this._tabs.reverse();

            this.tabs(this._tabs);
        },

        hasSelected: function (tabKey) {
            return this.selectedTab === tabKey;
        },

        addProductColsData: function (cols) {
            _.map(cols, function (item, index) {
                if (index !== '') {
                    item.index = index;

                    return utils.insert(item, this._productCols);
                }
            }.bind(this));

            this.productCols(this._productCols);
            this.initBookmarks(this._productCols);
        },

        addDefaultColumnsData: function (cols) {
            this.initBookmarks(cols);
        },

        initBookmarks: function (cols) {
            let initBookmarkColumns = function (columns) {
                _.each(cols, function (column) {
                    if (columns[column.index] === undefined) {
                        columns[column.index] = {
                            'sorting': false,
                            'visible': column.visible,
                            'gridjunior_label': column.gridjunior_label
                        };
                    }
                });
            };

            registry.get(
                'ineiman_salesgrid_listing.ineiman_salesgrid_listing.listing_top.bookmarks_storage',
                function () {
                    initBookmarkColumns(this.storage().current.columns);
                    _.each(this.storage().views, function (view) {
                        if (view.data) {
                            initBookmarkColumns(view.data.columns);
                        }
                    });
                }.bind(this)
            );
        },

        isVisibleTab: function () {
            return this._parent.getColumns(this.key).length > 0;
        },

        getTabs: function () {
            return this.tabs.filter('visible');
        },

        getColumns: function (tab) {
            let cols;

            if (tab === 'product') {
                cols = this.productCols();
            } else {
                cols = this.elems.filter(function (col) {
                    let ret = false;

                    if (tab === 'unassigned' && !col.tab && col.index !== 'grid_junior_items_ordered') {
                        ret = true;
                    } else if (col.tab === tab) {
                        ret = true;
                    }

                    return ret;
                });
            }

            return cols;
        },

        reloadGridData: function (data) {
            this.productCols(this.productCols());
            this.saveBookmark();

            if (data.visible === false) {
                return this;
            }

            let currentData = this.source().get('params');

            currentData.data = JSON.stringify({ 'column': data.index });
            this.client()
                .save(currentData)
                .done(this.gridJuniorReload)
                .fail(this.onSaveError);

            return this;
        },

        saveBookmark: function () {
            this.prepareColumns();
            this.storage().saveState();
            this.showItemsOrderedColumn();
            this.storage().hasChanges = true;
        },

        gridJuniorReload: function () {
            registry.get('index = ineiman_salesgrid_listing').source.reload();
        },

        prepareColumns: function () {
            let columns = this;

            this.elems.each(function (el) {
                let current = columns.storage().get('current.columns.' + el.index);

                el.label = el.gridjunior_label;

                if (current) {
                    current.visible = el.visible;
                    current.gridjunior_label = el.gridjunior_label;
                }
            });

            this.productCols.each(function (el) {
                let current = columns.storage().get('current.columns.' + el.index);

                if (current) {
                    current.visible = el.visible;
                    current.gridjunior_label = el.gridjunior_label;
                }
            });

            this.productCols(this.productCols());
        },

        countVisible: function () {
            return this.elems.filter('visible').length + this.productCols.filter('visible').length;
        },

        showItemsOrderedColumn: function () {
            let visibleColumns = _.filter(this.productCols(), function (el) {
                    return el.visible === true;
                });
            let cols = this.elems.filter(function (el) {
                    return el.index === 'grid_junior_items_ordered';
                });

            if (cols[0]) {
                cols[0].visible = visibleColumns.length > 0;
            }
        },

        initElement: function (el) {
            el.track(['gridjunior_label', 'label']);
        }
    });
});
