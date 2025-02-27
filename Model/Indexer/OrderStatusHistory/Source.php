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

namespace Ineiman\SalesGrid\Model\Indexer\OrderStatusHistory;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\DB\Select;
use ReturnTypeWillChange;
use Traversable;

/**
 * Order Status History Source to parse fields
 */
class Source implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var \Ineiman\SalesGrid\Model\Indexer\OrderStatusHistory\Collection
     */
    private Collection $orderStatusHistoryCollection;

    /**
     * Construct
     *
     * @param \Ineiman\SalesGrid\Model\Indexer\OrderStatusHistory\CollectionFactory $collectionFactory
     * @param int $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly int $batchSize = 10000
    ) {
        $this->orderStatusHistoryCollection = $collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getMainTable(): ?string
    {
        return $this->orderStatusHistoryCollection->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function getIdFieldName(): string
    {
        return $this->orderStatusHistoryCollection->getIdFieldName();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->orderStatusHistoryCollection->addFieldToSelect($fieldName, $alias);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelect(): Select
    {
        return $this->orderStatusHistoryCollection->getSelect();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->orderStatusHistoryCollection->addFieldToFilter($attribute, $condition);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->orderStatusHistoryCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return Traversable
     */
    #[ReturnTypeWillChange] public function getIterator()
    {
        $this->orderStatusHistoryCollection->setPageSize($this->batchSize);
        $lastPage = $this->orderStatusHistoryCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->orderStatusHistoryCollection->clear();
            $this->orderStatusHistoryCollection->setCurPage($pageNumber);
            foreach ($this->orderStatusHistoryCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }
}
