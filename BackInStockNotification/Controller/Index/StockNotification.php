<?php
namespace RLTSquare\BackInStockNotification\Controller\Index;

use http\Message;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use RLTSquare\BackInStockNotification\Model\StockApiCall\ApiCalls;

class StockNotification extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var ApiCalls
     */
    protected $apiCall;
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;


    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param ApiCalls $apiCall
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        ApiCalls $apiCall,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->apiCall = $apiCall;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\Message\ManagerInterface
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->messageManager->addErrorMessage(__('Subscription Failed'));
        }
        $requestData = $this->getRequest()->getParams();
        $customerEmail = $requestData['email'];
        $parentProductId = $requestData['parentProductId'];
        $childProductId = $requestData['childProductId'];
        $websiteId = $requestData['websiteId'];
        $size = $requestData['size'];

        // Api call here for subscription
        $result = $this->apiCall->apiRequest($customerEmail,$childProductId,$size,$parentProductId,$websiteId);
        return $this->resultJsonFactory->create()->setData([
            'response'=> $result
        ]);
    }

}
