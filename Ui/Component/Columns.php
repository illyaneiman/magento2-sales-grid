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

namespace Ineiman\SalesGrid\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Component\Container;

/**
 * Class to prepare grid columns
 */
class Columns extends Container
{
    /**
     * Bookmark name space
     */
    private const BOOKMARK_NAMESPACE = 'ineiman_salesgrid_listing';

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
        $columnsConfig = $this->getData('config');

        if (array_key_exists('productColsData', $columnsConfig)) {
            $bookmark = $this->bookmarkManagement->getByIdentifierNamespace(
                'current',
                self::BOOKMARK_NAMESPACE
            );
            $config = $bookmark?->getConfig();
            $bookmarksCols = [];

            if (isset($config['current']['columns']) && is_array($config)) {
                $bookmarksCols = $config['current']['columns'];
            }

            foreach ($columnsConfig['productColsData'] as $id => &$config) {
                if (isset($bookmarksCols[$id]['gridjunior_label'])) {
                    $config['gridjunior_label'] = $bookmarksCols[$id]['gridjunior_label'];
                    $config['default_label'] = $config['label'];
                } elseif (isset($config['label'])) {
                    $config['gridjunior_label'] = $config['default_label'] = $config['label'];
                }

                if (isset($bookmarksCols[$id]['visible'])) {
                    $config['visible'] = $bookmarksCols[$id]['visible'];
                } elseif (isset($config['visible'])) {
                    $config['visible'] = true;
                }
            }

            $this->setData('config', $columnsConfig);
        }
    }
}
