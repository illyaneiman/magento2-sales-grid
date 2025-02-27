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

namespace Ineiman\SalesGrid\Model\Export;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

trait ExportTrait
{
    /**
     * Get items
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface $dataProvider
     * @param array $availableProductDetails
     * @return \Magento\Framework\Api\Search\DocumentInterface[]
     */
    public function getDataProviderItems(DataProviderInterface $dataProvider, array $availableProductDetails)
    {
        $items = $dataProvider->getSearchResult()->getItems();
        $data = $dataProvider->getData();
        $dataItems = array_key_exists('items', $data) ? $data['items'] : [];
        foreach ($items as $item) {
            foreach ($dataItems as $dataItem) {
                if ($dataItem['entity_id'] == $item['entity_id']) {
                    if (array_key_exists('grid_junior_items_ordered', $dataItem)) {
                        $dataItem['grid_junior_items_ordered'] = $this->getOrderedItemsData(
                            $dataItem['grid_junior_items_ordered'],
                            $availableProductDetails
                        );
                    }
                    $item->setData($dataItem);
                    break;
                }
            }
        }

        return $items;
    }

    /**
     * Group products data
     *
     * @param array $orderedItems
     * @param array $availableProductDetails
     * @return string
     */
    private function getOrderedItemsData(array $orderedItems, array $availableProductDetails): string
    {
        $productDetailsColumn = [];
        foreach ($orderedItems as $productInfo) {
            foreach ($productInfo as $key => $value) {
                if (array_key_exists($key, $availableProductDetails) && $value && !is_array($value)) {
                    $productDetailsColumn[] = $availableProductDetails[$key] . ':' . $value;
                }
            }
        }

        return implode('|', $productDetailsColumn);
    }

    /**
     * Get product details for available products
     *
     * @param array $config
     * @return array
     */
    private function getAvailableProductDetails($config): array
    {
        $availableProductDetails = [];
        if (is_array($config) && isset($config['current']['columns'])) {
            $bookmarksCols = $config['current']['columns'];
            foreach ($bookmarksCols as $key => $colItem) {
                if (!empty($colItem['visible']) && stripos($key, 'product_sales_order') !== false) {
                    $availableProductDetails[$key] = $colItem['gridjunior_label'] ?? $key;
                }
            }
        }

        return $availableProductDetails;
    }
}
