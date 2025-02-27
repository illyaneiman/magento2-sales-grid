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

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\DB\Select;
use ReturnTypeWillChange;
use Traversable;

/**
 * Order Status Source to parse fields
 */
class Source implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var \Ineiman\SalesGrid\Model\Indexer\OrderStatus\Collection
     */
    private Collection $salesOrderStatusCollection;

    /**
     * Construct
     *
     * @param \Ineiman\SalesGrid\Model\Indexer\OrderStatus\CollectionFactory $collectionFactory
     * @param int $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly int $batchSize = 10000
    ) {
        $this->salesOrderStatusCollection = $collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getMainTable(): ?string
    {
        return $this->salesOrderStatusCollection->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function getIdFieldName(): string
    {
        return $this->salesOrderStatusCollection->getIdFieldName();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->salesOrderStatusCollection->addFieldToSelect($fieldName, $alias);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelect(): Select
    {
        return $this->salesOrderStatusCollection->getSelect();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->salesOrderStatusCollection->addFieldToFilter($attribute, $condition);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->salesOrderStatusCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return Traversable
     */
    #[ReturnTypeWillChange] public function getIterator()
    {
        $this->salesOrderStatusCollection->setPageSize($this->batchSize);
        $lastPage = $this->salesOrderStatusCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->salesOrderStatusCollection->clear();
            $this->salesOrderStatusCollection->setCurPage($pageNumber);
            foreach ($this->salesOrderStatusCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }
}
