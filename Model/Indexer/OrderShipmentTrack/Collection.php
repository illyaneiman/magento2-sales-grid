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

namespace Ineiman\SalesGrid\Model\Indexer\OrderShipmentTrack;

use Magento\Framework\DataObject;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection as ShipmentTrackCollection;

/**
 * Order Shipment Track Collection Class
 */
class Collection extends ShipmentTrackCollection
{
    /**
     * @inheritdoc
     */
    protected function beforeAddLoadedItem(DataObject $item): DataObject
    {
        return $item;
    }
}
