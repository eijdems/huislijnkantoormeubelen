<?php

namespace Emizen\Download\Block;
use Magento\Catalog\Model\ProductCategoryList;

class Customsession extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public $storeManager;
    public $scopeConfig;
    protected $_registry;
    protected $productCategory;
    protected $customerSession;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
 
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        ProductCategoryList $productCategory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->storeManager     = $storeManager;
        $this->scopeConfig      = $scopeConfig;
        $this->_registry        = $registry;
        $this->productCategory  = $productCategory;
        $this->customerSession  = $customerSession;
        $this->_messageManager  = $messageManager;
        parent::__construct($context, $data);
    }
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
   
    /**
     * get all the category id
     *
     * @param int $productId
     * @return array
     */
    
    public function getSession()
    {
        return $this->customerSession;
    }
    public function getGroupId(){
        return $this->customerSession->getCustomer()->getGroupId();
    }
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
}