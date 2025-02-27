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

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\ConvertToCsv as ExportConvertToCsv;
use Magento\Ui\Model\Export\MetadataProvider;
use Psr\Log\LoggerInterface;

/**
 * Class which exports data to Csv file
 */
class ConvertToCsv extends ExportConvertToCsv
{
    use ExportTrait;

    /**
     * Bookmark name space
     */
    private const BOOKMARK_NAMESPACE = 'ineiman_salesgrid_listing';

    /**
     * Construct
     *
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Ui\Model\Export\MetadataProvider $metadataProvider
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement
     * @param \Magento\Framework\Math\Random $random
     * @param \Psr\Log\LoggerInterface $logger
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filter $filter,
        MetadataProvider $metadataProvider,
        Filesystem $filesystem,
        private readonly BookmarkManagementInterface $bookmarkManagement,
        private readonly Random $random,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider);
    }

    /**
     * Get Csv file with data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile(): array
    {
        $component = $this->filter->getComponent();
        $bookmark = $this->bookmarkManagement->getByIdentifierNamespace(
            'current',
            self::BOOKMARK_NAMESPACE
        );
        $availableProductDetails = $this->getAvailableProductDetails($bookmark?->getConfig());

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $data = $this->prepareData($component);

        return [
            'type' => 'filename',
            'value' => $this->writeToCsvFile($availableProductDetails, $component, $data['fields'], $data['options']),
            'rm' => true
        ];
    }

    /**
     * Prepare data before writing to file
     *
     * @param \Magento\Framework\View\Element\UiComponentInterface $component
     * @return array
     * @throws LocalizedException
     */
    private function prepareData($component): array
    {
        return [
            'fields' => $this->metadataProvider->getFields($component),
            'options' => $this->metadataProvider->getOptions()
        ];
    }

    /**
     * Write data to Csv file
     *
     * @param array $availableProductDetails
     * @param \Magento\Framework\View\Element\UiComponentInterface $component
     * @param string[] $fields
     * @param array $options
     * @return string
     * @throws LocalizedException
     */
    private function writeToCsvFile($availableProductDetails, $component, $fields, $options): string
    {
        $dataProvider = $component->getContext()->getDataProvider();
        $fileName = $this->random->getUniqueHash();
        $file = 'export/'. $fileName . '.csv';

        try {
            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $stream->writeCsv($this->metadataProvider->getHeaders($component));

            $i = 1;
            $searchCriteria = $dataProvider->getSearchCriteria()
                ->setCurrentPage($i)
                ->setPageSize($this->pageSize);
            $totalCount = (int)$dataProvider->getSearchResult()->getTotalCount();

            while ($totalCount > 0) {
                $items = $this->getDataProviderItems($dataProvider, $availableProductDetails);
                foreach ($items as $item) {
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
                $searchCriteria->setCurrentPage(++$i);
                $totalCount = $totalCount - $this->pageSize;
            }

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
