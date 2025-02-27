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

namespace Ineiman\SalesGrid\Model\Indexer;

use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\Handler\AttributeHandler;
use Ineiman\SalesGrid\Model\Sales\Order\Grid;
use Magento\Framework\Indexer\FieldsetInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Attribute;

/**
 * Indexer attribute provider class
 */
class AttributeProvider implements FieldsetInterface
{
    /**
     * EAV entity
     */
    private const ENTITY = Grid::ENTITY;

    /**
     * @var Attribute[]
     */
    private array $attributes = [];

    /**
     * Construct
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        private readonly Config $eavConfig
    ) {
    }

    /**
     * Add EAV attribute fields to fieldset
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function addDynamicData(array $data): array
    {
        $additionalFields = $this->convert($this->getAttributes(), $data);
        $data['fields'] = $this->merge($data['fields'], $additionalFields);

        return $data;
    }

    /**
     * Retrieve all attributes
     *
     * @return Attribute[]
     * @throws LocalizedException
     */
    private function getAttributes(): array
    {
        if (empty($this->attributes)) {
            $this->attributes = [];
            $entityType = $this->eavConfig->getEntityType(static::ENTITY);
            /** @var Attribute[] $attributes */
            $attributes = $entityType->getAttributeCollection()->getItems();
            /** @var Customer $entity */
            $entity = $entityType->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }
            $this->attributes = $attributes;
        }

        return $this->attributes;
    }

    /**
     * Convert attributes to fields
     *
     * @param Attribute[] $attributes
     * @param array $fieldset
     * @return array
     */
    private function convert(array $attributes, array $fieldset): array
    {
        $fields = [];
        foreach ($attributes as $attribute) {
            if (!$attribute->isStatic()) {
                if ($attribute->getData('is_used_in_grid')) {
                    $fields[$attribute->getName()] = [
                        'name' => $attribute->getName(),
                        'handler' => AttributeHandler::class,
                        'origin' => $attribute->getName(),
                        'type' => $this->getType($attribute),
                        'dataType' => $attribute->getBackendType(),
                        'filters' => [],
                        'entity' => static::ENTITY,
                        'bind' => $fieldset['references']['customer']['to'] ?? null,
                    ];
                }
            } else {
                $fields[$attribute->getName()] = [
                    'type' => $this->getType($attribute),
                ];
            }
        }

        return $fields;
    }

    /**
     * Get field type for attribute
     *
     * @param Attribute $attribute
     * @return string
     */
    private function getType(Attribute $attribute): string
    {
        if ($attribute->canBeSearchableInGrid()) {
            $type = 'searchable';
        } elseif ($attribute->canBeFilterableInGrid()) {
            $type = 'filterable';
        } else {
            $type = 'virtual';
        }

        return $type;
    }

    /**
     * Merge fields with attribute fields
     *
     * @param array $dataFields
     * @param array $searchableFields
     * @return array
     */
    private function merge(array $dataFields, array $searchableFields): array
    {
        foreach ($searchableFields as $name => $field) {
            if (!isset($field['name']) && !isset($dataFields[$name])) {
                continue;
            }
            if (!isset($dataFields[$name])) {
                $dataFields[$name] = [];
            }
            foreach ($field as $key => $value) {
                $dataFields[$name][$key] = $value;
            }
        }

        return $dataFields;
    }
}
