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

namespace Ineiman\SalesGrid\Model\ResourceModel\Sales\Order\Grid;

use Ineiman\SalesGrid\Model\ResourceModel\SalesOrderGrid as ResourceModel;
use Ineiman\SalesGrid\Model\Sales\Order\Grid;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Order Grid Collection class
 */
class Collection extends AbstractCollection
{
    /**
     * Identifier field name for collection items
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Grid::class, ResourceModel::class);
    }
}
