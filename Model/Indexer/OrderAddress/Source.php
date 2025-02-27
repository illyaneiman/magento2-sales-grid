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

namespace Ineiman\SalesGrid\Model\Indexer\OrderAddress;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\DB\Select;
use ReturnTypeWillChange;
use Traversable;

/**
 * Order Address Source to parse fields
 */
class Source implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var \Ineiman\SalesGrid\Model\Indexer\OrderAddress\Collection
     */
    private Collection $orderAddressCollection;

    /**
     * Construct
     *
     * @param \Ineiman\SalesGrid\Model\Indexer\OrderAddress\CollectionFactory $collectionFactory
     * @param int $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly int $batchSize = 10000
    ) {
        $this->orderAddressCollection = $collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getMainTable(): ?string
    {
        return $this->orderAddressCollection->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function getIdFieldName(): string
    {
        return $this->orderAddressCollection->getIdFieldName();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->orderAddressCollection->addFieldToSelect($fieldName, $alias);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelect(): Select
    {
        return $this->orderAddressCollection->getSelect();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->orderAddressCollection->addFieldToFilter($attribute, $condition);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->orderAddressCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return Traversable
     */
    #[ReturnTypeWillChange] public function getIterator()
    {
        $this->orderAddressCollection->setPageSize($this->batchSize);
        $lastPage = $this->orderAddressCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->orderAddressCollection->clear();
            $this->orderAddressCollection->setCurPage($pageNumber);
            foreach ($this->orderAddressCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }
}
