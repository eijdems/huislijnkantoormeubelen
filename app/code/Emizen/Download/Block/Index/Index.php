<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\Download\Block\Index;

//use FME\Productattachments\Model\Productattachments;

class Index extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    protected $request;
    
    protected $_storeManager;
    
    protected $_categoryCollection;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \FME\Productattachments\Model\Productattachments $Productattachment,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        array $data = []
    ) { 
        $this->request = $request;
        $this->productattachment = $Productattachment;
        $this->_storeManager = $storeManager;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {   
        if ($this->request->getParams()) {
            $productId = $this->request->getParam('product_id');
            $this->atttachmentdata($productId);
        }
        return parent::_prepareLayout();
    }
    public function atttachmentdata($productId){

        $productData = $this->productattachment->getRelatedAttachments($productId);
        foreach ($productData as $productDatas) {
            $filename = $productDatas['filename'];
            return $productData;
        }        
    }
    public function getCategories(){
        $categories = $this->_categoryCollection->create()                              
            ->addAttributeToSelect('*')
            ->setStore($this->_storeManager->getStore()); //categories from current store will be fetched
        return $categories;
    }
}

