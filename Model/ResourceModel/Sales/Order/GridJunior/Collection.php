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

namespace Ineiman\SalesGrid\Model\ResourceModel\Sales\Order\GridJunior;

use Ineiman\SalesGrid\Model\Sales\Order\GridJunior;
use Ineiman\SalesGrid\Model\ResourceModel\SalesOrderGridJunior as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Order JuniorGrid Collection
 */
class Collection extends AbstractCollection
{
    /**
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
        $this->_init(GridJunior::class, ResourceModel::class);
    }
}
