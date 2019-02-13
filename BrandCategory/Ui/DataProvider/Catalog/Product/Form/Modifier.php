<?php

namespace GoMage\BrandCategory\Ui\DataProvider\Catalog\Product\Form;

/**
 * @codeCoverageIgnore
 */
class Modifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Categories
{
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->arrayManager->merge(
            'product-details/children/container_brand_category_id/children/brand_category_id',
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'select',
                            'componentType' => 'field',
                            'component' => 'Magento_Catalog/js/components/new-category',
                            'filterOptions' => true,
                            'chipsEnabled' => true,
                            'disableLabel' => true,
                            'levelsVisibility' => '1',
                            'multiple' => false,
                            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                            'options' => $this->getCategoriesTree(),
                            'listens' => [
                                'index=create_category:responseData' => 'setParsed',
                                'newOption' => 'toggleOptionSelected'
                            ],
                            'config' => [
                                'dataScope' => 'brand_category_id',
                                'sortOrder' => 10,
                            ],
                        ],
                    ],
                ]
            ]
        );
 
        return $meta;
    }
}
