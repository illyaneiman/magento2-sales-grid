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

namespace Ineiman\SalesGrid\Model\Indexer\OrderStatus;

use Magento\Sales\Model\ResourceModel\Status\Collection as SalesOrderStatusCollection;
use Magento\Framework\DataObject;

/**
 * Order Status Collection Class
 */
class Collection extends SalesOrderStatusCollection
{
    /**
     * @inheritdoc
     */
    protected function beforeAddLoadedItem(DataObject $item): DataObject
    {
        return $item;
    }
}
