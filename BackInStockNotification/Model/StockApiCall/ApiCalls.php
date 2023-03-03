<?php
namespace RLTSquare\BackInStockNotification\Model\StockApiCall;

use Magento\Framework\HTTP\Client\Curl;
use RLTSquare\BackInStockNotification\Helper\ApiConfig;
use RLTSquare\BackInStockNotification\Logger\Logger;
use Magento\Framework\Message\ManagerInterface;


class ApiCalls
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ApiConfig
     */
    protected $helper;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Curl $curl
     * @param ApiConfig $helper
     * @param Logger $logger
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Curl $curl,
        ApiConfig $helper,
        Logger $logger,
        ManagerInterface $messageManager
    ) {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    /**
     * @param $data
     * @param $URL
     * @param $headers
     * @param $options
     * @return string
     */
    public function curlPostRequest($data,$URL,$headers,$options)
    {

        $jsonData = json_encode($data);
        //set curl options
        $this->curl->setOptions($options);

        //set curl header
        $this->curl->setHeaders($headers);

        //post request with url and data
        $this->curl->post($URL, $jsonData);

        //read response
        return $this->curl->getBody();
    }

    /**
     * @param $URL
     * @param $headers
     * @param $options
     * @return string
     */
    public function curlGetRequest($URL,$headers,$options)
    {

        //set curl options
        $this->curl->setOptions($options);

        //set curl header
        $this->curl->setHeaders($headers);

        //post request with url and data
        $this->curl->get($URL);

        //read response
        return $this->curl->getBody();
    }

    /**
     * @param $customerEmail
     * @param $childProductId
     * @param $size
     * @param $parentProductId
     * @param $websiteId
     * @return ManagerInterface|string
     */
    public function apiRequest($customerEmail, $childProductId, $size, $parentProductId, $websiteId)
    {
        $apiBaseurl = $this->helper->getApiBaseUrl($store_id=null);
        if($apiBaseurl){
            $accessToken = $this->helper->getAccessToken($store_id=null);
            if($accessToken)
            {
                $this->logger->info('------------Back in stock for subscription---------');
                $this->logger->info('Customer Email: '.$customerEmail);
                $emailData = [
                    "product_id"=> $childProductId,
                    "size"=> $size,
                    "parent_id" => $parentProductId,
                    "customer_email"=> $customerEmail,
                    "website_id"=> $websiteId,
                    "is_notified"=> "0" //-> Initially submit as 0, it will change to 1 when the email is sent
                ];
                try{
                    $headers = [
                        "Content-type" => "application/json",
                        "Authorization" => $accessToken
                    ];
                    $options = [CURLOPT_RETURNTRANSFER => 1];
                    $result = $this->curlPostRequest($emailData,$apiBaseurl,$headers,$options);
                    $this->logger->info('Subscription Result' .$result .'for product:' .$childProductId);
                    return $result;
                }catch (\Exception $e){
                    $this->logger->critical($e->getMessage());
                    return $e->getMessage();
                }
            }else{
                return $this->messageManager->addErrorMessage(__('Subscription Failed'));
            }

        }else{
            return $this->messageManager->addErrorMessage(__('Subscription Failed ..'));
        }
    }

    /**
     * @param $productId
     * @return void
     */
    public function emailNotification($productId)
    {
        try{
            // getting base url of api
            $apiBaseUrl = $this->helper->getApiBaseUrl($store=null);
            if($apiBaseUrl){
                $notifyUrl = $apiBaseUrl.'/'.$productId.'/notify';
                // access token
                $accessToken = $this->helper->getAccessToken($store_id=null);
                if($accessToken)
                {
                    $options = [CURLOPT_RETURNTRANSFER => 1];
                    $headers = [
                        "Content-type" => "application/json",
                        "Authorization" => $accessToken
                    ];
                    // api call
                    $result = $this->curlGetRequest($notifyUrl,$headers,$options);
                    $this->logger->info("Result -" .$result);
                }
            }

        }catch  (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->logger->info($this->logger->critical($e->getMessage()));
        }
    }

}
