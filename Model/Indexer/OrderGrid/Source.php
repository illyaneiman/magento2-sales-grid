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

namespace Ineiman\SalesGrid\Model\Indexer\OrderGrid;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\DB\Select;
use ReturnTypeWillChange;
use Traversable;

/**
 * Order Grid Source to parse fields
 */
class Source implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var \Ineiman\SalesGrid\Model\Indexer\OrderGrid\Collection
     */
    private Collection $salesOrderGridCollection;

    /**
     * Construct
     *
     * @param \Ineiman\SalesGrid\Model\Indexer\OrderGrid\CollectionFactory $collectionFactory
     * @param int $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly int $batchSize = 10000
    ) {
        $this->salesOrderGridCollection = $collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getMainTable(): ?string
    {
        return $this->salesOrderGridCollection->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function getIdFieldName(): string
    {
        return $this->salesOrderGridCollection->getIdFieldName();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->salesOrderGridCollection->addFieldToSelect($fieldName, $alias);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelect(): Select
    {
        return $this->salesOrderGridCollection->getSelect();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->salesOrderGridCollection->addFieldToFilter('main_table.'.$attribute, $condition);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->salesOrderGridCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return Traversable
     */
    #[ReturnTypeWillChange] public function getIterator()
    {
        $this->salesOrderGridCollection->setPageSize($this->batchSize);
        $lastPage = $this->salesOrderGridCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->salesOrderGridCollection->clear();
            $this->salesOrderGridCollection->setCurPage($pageNumber);
            foreach ($this->salesOrderGridCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }
}
