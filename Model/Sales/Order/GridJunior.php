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

namespace Ineiman\SalesGrid\Model\Sales\Order;

use Ineiman\SalesGrid\Model\ResourceModel\SalesOrderGridJunior;
use Magento\Framework\Model\AbstractModel;

/**
 * Sales Order GridJunior Model class
 */
class GridJunior extends AbstractModel
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SalesOrderGridJunior::class);
    }
}
