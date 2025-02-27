<?php
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

declare(strict_types=1);

namespace Ineiman\SalesGrid\Ui\DataProvider\Sales\Order;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupFactory;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as Base;

/**
 * Sales Order Grid Data Provider Class
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class DataProvider extends Base
{
    /**
     * Product columns
     */
    private const PRODUCT_COLUMNS = [
        'product_sales_order_item_name' => 'product_sales_order_item_name',
        'product_sales_order_item_sku' => 'product_sales_order_item_sku',
        'product_sales_order_item_product_type' => 'product_sales_order_item_product_type',
        'product_sales_order_item_base_price' => 'product_sales_order_item_base_price',
        'product_sales_order_item_qty_ordered' => 'product_sales_order_item_qty_ordered',
        'product_sales_order_item_qty_invoiced' => 'product_sales_order_item_qty_invoiced',
        'product_sales_order_item_qty_shipped' => 'product_sales_order_item_qty_shipped',
        'product_sales_order_item_qty_refunded' => 'product_sales_order_item_qty_refunded',
        'product_sales_order_item_qty_canceled' => 'product_sales_order_item_qty_canceled',
        'product_sales_order_item_row_total' => 'product_sales_order_item_row_total'
    ];

    /**#@+
     * Constants for conditions
     */
    private const GREATER_THAN_CONDITION = 'gteq';
    private const LESS_THAN_CONDITION = 'lteq';
    /**#@-*/

    /**
     * Filters for default-grid fields
     *
     * @var array
     */
    private array $fieldCompareFilters = [];

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        private readonly ContextInterface $context,
        private readonly FilterGroupFactory $filterGroupFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->afterGetData(parent::getData());
    }

    /**
     * @inheritdoc
     */
    public function getSearchResult()
    {
        return $this->afterGetSearchResult(parent::getSearchResult());
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        return $this->afterGetSearchCriteria(parent::getSearchCriteria());
    }

    /**
     * Group items data
     *
     * @param array $data
     * @return array
     */
    private function afterGetData(array $data)
    {
        $orderItemData = [];
        foreach ($data['items'] as $item) {
            $orderItemData[$item['entity_id']] = $this->modifyOrderItemData($item);
        }

        $items = &$data['items'];
        foreach ($items as &$item) {
            if (array_key_exists($item['entity_id'], $orderItemData)) {
                $item['grid_junior_items_ordered'] = $orderItemData[$item['entity_id']];
            }
        }

        return $data;
    }

    /**
     * Additional logic for search by default columns or ProductDetails column
     *
     * @param \Magento\Framework\Api\Search\SearchResultInterface $searchResult
     * @return \Magento\Framework\Api\Search\SearchResultInterface
     */
    private function afterGetSearchResult(SearchResultInterface $searchResult)
    {
        /**
         * Filters for grid columns, except Product details
         */
        if (count($this->fieldCompareFilters)) {
            foreach ($this->fieldCompareFilters as $filter) {
                $searchResult->addFieldToFilter($filter->getField(), [
                    $filter->getConditionType() => $filter->getValue()
                ]);
            }
        } else {
            $filterData = $this->context->getFiltersParams();

            /**
             * Filters for Product details
             */
            foreach ($filterData as $key => $value) {
                if (array_key_exists($key, self::PRODUCT_COLUMNS)) {
                    $condition = [];
                    if (is_array($value)) {
                        /**
                         * Ranged filters in PD (example: qty ordered)
                         */
                        foreach ($value as $k => $v) {
                            $condition[$k] = $v;
                        }
                    } else {
                        /**
                         * Non-ranged filters in PD (example: name)
                         */
                        $condition = [
                            'like' => '%' . $value . '%'
                        ];
                    }

                    $searchResult->addFieldToFilter($key, $condition);
                }
            }
        }

        return $searchResult;
    }

    /**
     * Add filters depending on fields
     *
     * @param \Magento\Framework\Api\Search\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    private function afterGetSearchCriteria(SearchCriteria $searchCriteria)
    {
        $updatedFilterGroups = $this->extractFiltersForCompare($searchCriteria);
        if (count($this->fieldCompareFilters)) {
            $searchCriteria->setFilterGroups($updatedFilterGroups);
        }

        return $searchCriteria;
    }

    /**
     * Modify Order Item Data
     *
     * @param array $item
     * @return array
     */
    private function modifyOrderItemData($item)
    {
        $reorderedData = [];
        foreach ($item as $attributeKey => $values) {
            if (str_contains($attributeKey, 'product_sales_order_')) {
                $values = array_slice(
                    explode(',', $values),
                    0,
                    (int)$item['general_sales_order_total_item_count']
                );

                foreach ($values as $key => $value) {
                    $reorderedData[$key][$attributeKey] = $value;
                }
            }
        }

        return $reorderedData;
    }

    /**
     * Extract and add filters for Product Details field
     *
     * @param \Magento\Framework\Api\Search\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\Search\FilterGroup[]
     */
    private function extractFiltersForCompare(SearchCriteria $searchCriteria): array
    {
        $newFilterGroups = [];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $newFilters = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $conditionType = $filter->getConditionType();
                $filterValue = $filter->getValue();
                $isGreaterThan = $conditionType == self::GREATER_THAN_CONDITION && $filterValue == 0;
                $isLessThan = $conditionType == self::LESS_THAN_CONDITION && $filterValue >= 0;
                if ($isGreaterThan || $isLessThan) {
                    $this->fieldCompareFilters[] = $filter;
                } else {
                    $newFilters[] = $filter;
                }
            }

            if (count($newFilters)) {
                $newFilterGroups[] = $this->filterGroupFactory->create()->setFilters($newFilters);
            }
        }

        return $newFilterGroups;
    }
}
