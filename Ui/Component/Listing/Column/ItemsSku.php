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

namespace Ineiman\SalesGrid\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * ItemsSku field class
 */
class ItemsSku extends Column
{
    /**
     * Prepare data source for ItemsSku column
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['general_sales_order_items_sku'])) {
                    $skus = explode(',', $item['general_sales_order_items_sku']);
                    $item['general_sales_order_items_sku'] = '';
                    foreach ($skus as $sku) {
                        $item['general_sales_order_items_sku'] .= '<p>' . $sku . '</p>';
                    }
                }
            }
        }

        return $dataSource;
    }
}
