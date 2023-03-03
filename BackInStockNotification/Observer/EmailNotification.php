<?php

namespace RLTSquare\BackInStockNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use RLTSquare\BackInStockNotification\Helper\ApiConfig;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use RLTSquare\BackInStockNotification\Logger\Logger;
use RLTSquare\BackInStockNotification\Model\StockApiCall\ApiCalls;


class EmailNotification implements ObserverInterface
{
    /**
     * @var ApiConfig
     */
    protected $helper;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var StockRegistryInterface|null
     */
    private $stockRegistry;
    /**
     * @var ApiCalls
     */
    public $apiCalls;

    /**
     * @param ApiConfig $apiConfig
     * @param Logger $logger
     */
    public function __construct(
        ApiConfig $apiConfig,
        Logger $logger,
        StockRegistryInterface $stockRegistry,
        ApiCalls $apiCalls
    )
    {
        $this->logger = $logger;
        $this->helper = $apiConfig;
        $this->stockRegistry = $stockRegistry;
        $this->apiCalls = $apiCalls;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isEnabled = $this->helper->isEnabled();
        if($isEnabled)
        {
            // Load product here
            $_product = $observer->getProduct();
            $productId=$_product->getId();
            $originalStockData  = $_product->getOrigData('quantity_and_stock_status');
            if($originalStockData && $originalStockData['is_in_stock'] == false)
            {
                $isInStock = $this->getStockStatus($productId);
                if($isInStock){
                    $this->logger->info('Email Notification For Product: '.$_product->getName());
                    $this->apiCalls->emailNotification($productId);
                }
            }
        }



    }

    /**
     * @param $productId
     * @return bool|int
     */
    public function getStockStatus($productId)
    {
        /** @var StockItemInterface $stockItem */
        $stockItem = $this->stockRegistry->getStockItem($productId);
        $isInStock = $stockItem ? $stockItem->getIsInStock() : false;
        return $isInStock;
    }
}
