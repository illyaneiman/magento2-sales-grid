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

namespace Ineiman\SalesGrid\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Sales Order Grid Resource Model
 */
class SalesOrderGrid extends AbstractDb
{
    /**
     * Table name
     */
    public const TABLE_NAME = 'sales_order_grid';

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
