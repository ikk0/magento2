<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Ui\Component;

class FilterFactory
{
    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $componentFactory;

    /**
     * @var array
     */
    protected $filterMap = [
        'default' => 'filterInput',
        'select' => 'filterSelect',
        'boolean' => 'filterSelect',
        'multiselect' => 'filterSelect',
        'date' => 'filterDate',
    ];

    /**
     * @param \Magento\Framework\View\Element\UiComponentFactory $componentFactory
     */
    public function __construct(\Magento\Framework\View\Element\UiComponentFactory $componentFactory)
    {
        $this->componentFactory = $componentFactory;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @return \Magento\Ui\Component\Listing\Columns\ColumnInterface
     */
    public function create($attribute, $context)
    {
        $columnName = $attribute->getAttributeCode();
        $config = [
            'dataScope' => $columnName,
            'label' => __($attribute->getDefaultFrontendLabel()),
        ];
        if ($attribute->usesSource()) {
            $config['options'] = $attribute->getSource()->getAllOptions();
            $config['caption'] = __('Select...');
        }
        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create($columnName, $this->getFilterType($attribute), $arguments);
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @return string
     */
    protected function getFilterType($attribute)
    {
        return isset($this->filterMap[$attribute->getFrontendInput()])
            ? $this->filterMap[$attribute->getFrontendInput()]
            : $this->filterMap['default'];
    }
}
