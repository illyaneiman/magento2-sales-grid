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

namespace Ineiman\SalesGrid\Model\Indexer\Handler;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Indexer\HandlerInterface;

/**
 * Class for preparing SQL query for indexer
 */
class ColumnsHandler implements HandlerInterface
{
    /**
     * Construct
     *
     * @param string $columnName
     * @param string $source
     */
    public function __construct(
        private readonly string $columnName,
        private readonly string $source
    ) {
    }

    /**
     * @inheritdoc
     */
    public function prepareSql(SourceProviderInterface $source, $alias, $fieldInfo)
    {
        if (!empty($this->columnName) && !empty($this->source)) {
            $source->getSelect()->columns([
                $this->columnName => new \Zend_Db_Expr(
                    "group_concat(" . $this->source . " SEPARATOR ',')"
                )
            ]);
        }
    }
}
