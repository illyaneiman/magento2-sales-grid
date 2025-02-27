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

namespace Ineiman\SalesGrid\Model\Indexer;

use Magento\Framework\Indexer\IndexerInterfaceFactory;
use Magento\Framework\Mview\ActionInterface as MviewInterface;

/**
 * Class for reindex
 */
class Indexer implements MviewInterface
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
     */
    public function __construct(
        private readonly IndexerInterfaceFactory $indexerFactory
    ) {
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     * @api
     */
    public function execute($ids)
    {
        $indexer = $this->indexerFactory->create()->load("ineiman_salesgrid");
        $indexer->reindexList($ids);
    }
}
