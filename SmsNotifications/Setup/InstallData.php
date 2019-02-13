<?php

namespace GoMage\SmsNotifications\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var State
     */
    protected $appState;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @param State $appState
     * @param WriterInterface $configWriter
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        State $appState,
        WriterInterface $configWriter,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->appState = $appState;
        $this->configWriter = $configWriter;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'doInstall'], [$setup, $context]);
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function doInstall(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'send_sms',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Send SMS',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'sort_order' => 100,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
            ]
        );

        $setup->endSetup();
    }
}
