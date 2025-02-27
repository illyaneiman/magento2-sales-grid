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

namespace Ineiman\SalesGrid\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Component\Listing\Columns as ListingColumns;

/**
 * Class to prepare grid columns
 */
class Columns extends ListingColumns
{
    /**
     * Bookmark name space
     */
    public const BOOKMARK_NAMESPACE = 'ineiman_salesgrid_flat';

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        private readonly BookmarkManagementInterface $bookmarkManagement,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $this->prepareColumns();
    }

    /**
     * Prepare component columns configuration
     *
     * @return void
     */
    private function prepareColumns()
    {
        $bookmark = $this->bookmarkManagement->getByIdentifierNamespace(
            'current',
            self::BOOKMARK_NAMESPACE
        );
        $config = $bookmark?->getConfig();
        $bookmarksCols = [];

        if (isset($config['current']['columns']) && is_array($config)) {
            $bookmarksCols = $config['current']['columns'];
        }

        foreach ($this->getChildComponents() as $id => $column) {
            if ($column instanceof ListingColumns\Column) {
                $config = $column->getData('config');

                if (isset($bookmarksCols[$id], $bookmarksCols[$id]['gridjunior_label'])) {
                    $config['gridjunior_label'] = $bookmarksCols[$id]['gridjunior_label'];
                    $config['default_label'] = $config['label'];
                    $config['label'] = $bookmarksCols[$id]['gridjunior_label'];
                } elseif (isset($config['label'])) {
                    $config['gridjunior_label'] = $config['default_label'] = $config['label'];
                }

                if (isset($bookmarksCols[$id], $bookmarksCols[$id]['visible'])) {
                    $config['visible'] = $bookmarksCols[$id]['visible'];
                } elseif (isset($config['visible'])) {
                    $config['visible'] = true;
                }

                $column->setData('config', $config);
            }
        }
    }
}
