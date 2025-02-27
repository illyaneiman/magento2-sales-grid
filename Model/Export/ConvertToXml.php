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

namespace Ineiman\SalesGrid\Model\Export;

use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\ConvertToXml as ExportConvertToXml;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\SearchResultIteratorFactory;
use Psr\Log\LoggerInterface;

/**
 * Class which exports data to Xml file
 */
class ConvertToXml extends ExportConvertToXml
{
    use ExportTrait;

    /**
     * Bookmark name space
     */
    private const BOOKMARK_NAMESPACE = 'ineiman_salesgrid_listing';

    /**
     * ConvertToXml constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Ui\Model\Export\MetadataProvider $metadataProvider
     * @param \Magento\Framework\Convert\ExcelFactory $excelFactory
     * @param \Magento\Ui\Model\Export\SearchResultIteratorFactory $iteratorFactory
     * @param \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement
     * @param \Magento\Framework\Math\Random $random
     * @param \Psr\Log\LoggerInterface $logger
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ExcelFactory $excelFactory,
        SearchResultIteratorFactory $iteratorFactory,
        private readonly BookmarkManagementInterface $bookmarkManagement,
        private readonly Random $random,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider, $excelFactory, $iteratorFactory);
    }

    /**
     * Get Xml file with data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getXmlFile(): array
    {
        $component = $this->filter->getComponent();
        $bookmark = $this->bookmarkManagement->getByIdentifierNamespace(
            'current',
            self::BOOKMARK_NAMESPACE
        );
        $availableProductDetails = $this->getAvailableProductDetails($bookmark?->getConfig());
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $component->getContext()->getDataProvider()->setLimit(0, 0);
        $searchResultIterator = $this->prepareData($component, $availableProductDetails);

        return [
            'type' => 'filename',
            'value' => $this->writeToXmlFile($searchResultIterator, $component),
            'rm' => true
        ];
    }

    /**
     * Prepare data to write to file
     *
     * @param \Magento\Framework\View\Element\UiComponentInterface $component
     * @param array $availableProductDetails
     * @return \Magento\Ui\Model\Export\SearchResultIterator
     */
    private function prepareData($component, $availableProductDetails)
    {
        $searchResultItems = $this->getDataProviderItems(
            $component->getContext()->getDataProvider(),
            $availableProductDetails
        );
        $this->prepareItems($component->getName(), $searchResultItems);

        return $this->iteratorFactory->create(['items' => $searchResultItems]);
    }

    /**
     * Write data to Xml file
     *
     * @param \Magento\Ui\Model\Export\SearchResultIterator $searchResultIterator
     * @param \Magento\Framework\View\Element\UiComponentInterface $component
     * @return string
     * @throws LocalizedException
     */
    private function writeToXmlFile($searchResultIterator, $component)
    {
        $name = $this->random->getUniqueHash();
        $file = 'export/'. $name . '.xml';

        try {
            $excel = $this->excelFactory->create([
                'iterator' => $searchResultIterator,
                'rowCallback'=> [$this, 'getRowData'],
            ]);
            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $excel->setDataHeader($this->metadataProvider->getHeaders($component));
            $excel->write($stream, $component->getName() . '.xml');
            $stream->unlock();
            $stream->close();
        } catch (FileSystemException $e) {
            $this->logger->error('FileSystemException: ' . $e->getMessage());
            $file = null;
        } catch (LocalizedException $e) {
            $this->logger->error('LocalizedException: ' . $e->getMessage());
            $file = null;
        } catch (\Exception $e) {
            $this->logger->error('Exception: ' . $e->getMessage());
            $file = null;
        }

        return $file;
    }
}
