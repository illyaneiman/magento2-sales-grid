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

namespace Ineiman\SalesGrid\Model\Indexer\OrderShipmentTrack;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\DB\Select;
use ReturnTypeWillChange;
use Traversable;

/**
 * Order Shipment Track Source to parse fields
 */
class Source implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var \Ineiman\SalesGrid\Model\Indexer\OrderShipmentTrack\Collection
     */
    private Collection $orderShipmentTrackCollection;

    /**
     * Construct
     *
     * @param \Ineiman\SalesGrid\Model\Indexer\OrderShipmentTrack\CollectionFactory $collectionFactory
     * @param int $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        private readonly int $batchSize = 10000
    ) {
        $this->orderShipmentTrackCollection = $collectionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getMainTable(): ?string
    {
        return $this->orderShipmentTrackCollection->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function getIdFieldName(): string
    {
        return $this->orderShipmentTrackCollection->getIdFieldName();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->orderShipmentTrackCollection->addFieldToSelect($fieldName, $alias);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelect(): Select
    {
        return $this->orderShipmentTrackCollection->getSelect();
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->orderShipmentTrackCollection->addFieldToFilter($attribute, $condition);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->orderShipmentTrackCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return Traversable
     */
    #[ReturnTypeWillChange] public function getIterator()
    {
        $this->orderShipmentTrackCollection->setPageSize($this->batchSize);
        $lastPage = $this->orderShipmentTrackCollection->getLastPageNumber();
        $pageNumber = 1;
        do {
            $this->orderShipmentTrackCollection->clear();
            $this->orderShipmentTrackCollection->setCurPage($pageNumber);
            foreach ($this->orderShipmentTrackCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }
}
