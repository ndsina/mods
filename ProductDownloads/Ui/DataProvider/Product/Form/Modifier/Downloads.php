<?php

namespace GoMage\ProductDownloads\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads as DownloadsAttribute;
use Magento\Ui\Component\Container;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;

class Downloads extends AbstractModifier
{
    /**
     * @var DownloadsAttribute
     */
    protected $downloads;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param DownloadsAttribute $downloads
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        DownloadsAttribute $downloads
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->downloads = $downloads;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeDownloadsField($meta);
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     *
     * @param array $meta
     * @return array
     */
    protected function customizeDownloadsField(array $meta)
    {
        $downloads = $this->getDownloads();

        $fieldCode = DownloadsAttribute::CODE;
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }

        $containerPath = $this->arrayManager->slicePath($elementPath, 0, -2);

        $meta = $this->arrayManager->merge(
            $elementPath . '/arguments/data/config',
            $meta,
            [
                'disabled' => 'disabled',
                'additionalClasses' => 'admin__field _hidden'
            ]
        );

        $children = [];
        $children['specification'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addbefore' => __('Specification Sheet'),
                        'formElement' => 'input',
                        'componentType' => 'field',
                        'dataType' => 'text',
                        'value' => $downloads->getSpecification(),
                    ],
                ],
            ],
        ];

        foreach ($downloads->getBrochure() as $key => $value) {
            $children['brochure' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('Brochure / Sell Sheet'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        foreach ($downloads->getParts() as $key => $value) {
            $children['parts' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('Parts / Catalog List'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        foreach ($downloads->getServiceManual() as $key => $value) {
            $children['service_manual' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('Service Manual'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        $children['owner_manual'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addbefore' => __('Owner Manual'),
                        'formElement' => 'input',
                        'componentType' => 'field',
                        'dataType' => 'text',
                        'value' => $downloads->getOwnerManual(),
                    ],
                ],
            ],
        ];

        foreach ($downloads->getVideo() as $key => $value) {
            $children['video' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('Video, Service Manual'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        foreach ($downloads->getCad() as $key => $value) {
            $children['cad' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('CAD Drawings'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        foreach ($downloads->getDiagram() as $key => $value) {
            $children['diagram' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('Diagram'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        foreach ($downloads->getBulletin() as $key => $value) {
            $children['bulletin' . $key] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'addbefore' => __('Service Bulletin'),
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'dataType' => 'text',
                            'value' => $value,
                        ],
                    ],
                ],
            ];
        }

        $meta = $this->arrayManager->merge($containerPath, $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'breakLine' => true,
                            'dataScope' => $fieldCode,
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'component' => 'Magento_Ui/js/form/components/group',
                        ],
                    ],
                ],
                'children'  => $children,
            ]
        );

        return $meta;
    }

    /**
     * @return DownloadsAttribute
     */
    protected function getDownloads()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $data = $product->getData(DownloadsAttribute::CODE);

        if ($data && is_string($data)) {
            $this->downloads->unserialize($data);
        }

        return $this->downloads;
    }
}