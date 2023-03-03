<?php
namespace RLTSquare\BackInStockNotification\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\Client\Curl;


class ApiConfig extends AbstractHelper
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    const IS_ENABLED = 'back_in_stock/in_stock/enabled_backinstock';
    const API_BASE_URL = 'back_in_stock/in_stock/api_base_url';
    const API_ACCESS_TOKEN = 'back_in_stock/in_stock/api_auth';

    /**
     * @param Context $context
     * @param ScopeConfigInterface $_scopeConfig
     * @param Curl $curl
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $_scopeConfig,
        Curl $curl
    ) {
        parent::__construct($context);
        $this->scopeConfig = $_scopeConfig;
        $this->curl = $curl;

    }


    protected function getConfigValue($path, $storeId = null) {
        return ($storeId)
            ? $this->scopeConfig->getValue($path, $storeId)
            : $this->scopeConfig->getValue($path);
    }
    /**
     * @param null $store_id
     * @return mixed
     */
    public function getApiBaseUrl($store_id = null)
    {
        return  $this->getConfigValue(self::API_BASE_URL, $store_id);
    }

    /**
     * @param $store_id
     * @return mixed
     */
    public function isEnabled($store_id = null)
    {
        return  $this->getConfigValue(self::IS_ENABLED, $store_id);
    }

    /**
     * @param $store_id
     * @return mixed
     */
    public function getAccessToken($store_id = null)
    {
        return  $this->getConfigValue(self::API_ACCESS_TOKEN, $store_id);
    }
}
