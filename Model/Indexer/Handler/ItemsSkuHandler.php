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

namespace Ineiman\SalesGrid\Model\Indexer\Handler;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Indexer\HandlerInterface;

/**
 * Class for preparing SQL query for indexer
 */
class ItemsSkuHandler implements HandlerInterface
{
    /**
     * @inheritdoc
     */
    public function prepareSql(SourceProviderInterface $source, $alias, $fieldInfo)
    {
        $source->getSelect()->columns([
            'general_sales_order_items_sku' => new \Zend_Db_Expr(
                "group_concat(DISTINCT product_sales_order_item.sku SEPARATOR ',')"
            )
        ]);
    }
}
