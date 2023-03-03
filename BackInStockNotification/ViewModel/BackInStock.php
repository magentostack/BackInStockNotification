<?php
namespace RLTSquare\BackInStockNotification\ViewModel;
use Magento\Customer\Model\SessionFactory;
use RLTSquare\BackInStockNotification\Helper\ApiConfig;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class BackInStock implements ArgumentInterface
{
    /**
     * @var SessionFactory
     */
    protected $cutomerSession;
    /**
     * @var ApiConfig
     */
    protected $helper;

    /**
     * @param SessionFactory $session
     * @param ApiConfig $helper
     */
    public function __construct(SessionFactory $session, ApiConfig $helper)
    {
        $this->cutomerSession = $session;
        $this->helper = $helper;
    }
    /**
     * @return string|null
     */
    public function getLoggedInCustomerEmail()
    {
        $customerSession = $this->cutomerSession->create();
        if($customerSession->isLoggedIn()){
            $customer = $customerSession->getCustomer();
            $customerEmail = $customer->getEmail();
            return $customerEmail;
        }
        return null;
    }
    /**
     * @return mixed
     */
    public function isEnabled()
    {
        $isEnabled = $this->helper->isEnabled($store_id=null);
        return $isEnabled;
    }
}
