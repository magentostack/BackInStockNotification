<?php

namespace RLTSquare\BackInStockNotification\Plugin\Model\ResourceModel\Attribute;

use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;
use \RLTSquare\BackInStockNotification\Helper\ApiConfig;

/**
 * Plugin for OptionSelectBuilderInterface to add stock status filter.
 */
class InStockOptionSelectBuilder
{
    /**
     * CatalogInventory Stock Status Resource Model.
     *
     * @var Status
     */
    private $stockStatusResource;
    protected $helper;


    /**
     * @param Status $stockStatusResource
     */
    public function __construct(Status $stockStatusResource,ApiConfig $helper)
    {
        $this->stockStatusResource = $stockStatusResource;
        $this->helper = $helper;
    }

    /**
     * Add stock status filter to select.
     *
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSelect(OptionSelectBuilderInterface $subject, Select $select)
    {
        $isEnabled = $this->helper->isEnabled($store_id=null);
        if($isEnabled){
            $select->joinInner(
                ['stock' => $this->stockStatusResource->getMainTable()],
                'stock.product_id = entity.entity_id',
                ['stock.stock_status']
            );
        }
        return $select;
    }
}
