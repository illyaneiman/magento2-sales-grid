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

namespace Ineiman\SalesGrid\Model\Indexer\Order;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\DB\Select;
use ReturnTypeWillChange;

/**
 * Order Source to parse fields
 */
class Source implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var Collection
     */
    private Collection $orderCollection;

    /**
     * Construct
     *
     * @param \Ineiman\SalesGrid\Model\Indexer\Order\CollectionFactory $collectionFactory
     * @param int $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly int $batchSize = 10000
    ) {
        $this->orderCollection = $collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getMainTable(): ?string
    {
        return $this->orderCollection->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function getIdFieldName(): string
    {
        return $this->orderCollection->getIdFieldName();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->orderCollection->addFieldToSelect($fieldName, $alias);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelect(): Select
    {
        return $this->orderCollection->getSelect();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->orderCollection->addFieldToFilter($attribute, $condition);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->orderCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return \Traversable
     */
    #[ReturnTypeWillChange] public function getIterator()
    {
        $this->orderCollection->setPageSize($this->batchSize);
        $lastPage = $this->orderCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->orderCollection->clear();
            $this->orderCollection->setCurPage($pageNumber);
            foreach ($this->orderCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }
}
