<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_Productattachments
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\Productattachments\Block;

class Productattachments extends \Magento\Framework\View\Element\Template
{
    public $_storeManager;
    public $_config;

    public $_coreresourceFactory;
    public $_mymoduleHelper;
    public $_coreresource;
    public $_coreRegistry;
    public $_objectManager;
    public $_productattachmentsproductattachmentsFactory;
    public $_productattachmentsproductattachments;

    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnectionFactory $coreresourceFactory,
        \Magento\Framework\App\ResourceConnection $coreresource,
        \FME\Productattachments\Model\ProductattachmentsFactory $productattachmentsproductattachmentsFactory,
        \FME\Productattachments\Model\Productattachments $productattachmentsproductattachments,
        \FME\Productattachments\Helper\Data $myModuleHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreresourceFactory = $coreresourceFactory;
        $this->_mymoduleHelper      = $myModuleHelper;
        $this->_coreresource        = $coreresource;
        $this->_storeManager        = $context->getStoreManager();
        $this->_coreRegistry        = $registry;
        $this->_objectManager       = $objectManager;
        $this->_config              = $context->getScopeConfig();
        $this->_productattachmentsproductattachmentsFactory = $productattachmentsproductattachmentsFactory;
        $this->_productattachmentsproductattachments        = $productattachmentsproductattachments;
    }//end __construct()
    public function ispro_pdflink()
    {
        return $this->_mymoduleHelper->ispro_pdflink();
    }//end ispro_pdflink()
    public function getProductRelatedAttachments($blockPosition = null, $categoryId = null)
    {
        $id = $this->getProduct()->getId();
        return $this->_productattachmentsproductattachments->getProductRelatedAttachments($blockPosition, $categoryId, $id);
    }//end getProductRelatedAttachments()
    public function getObjectManager()
    {
        return $this->_objectManager;
    }
    public function getCatsRelatedAttachments($blockPosition = null, $categoryId = null)
    {
        $id = $this->getCategory()->getId();
        return $this->_productattachmentsproductattachments->getRelatedCats($blockPosition, $categoryId, $id);
    }//end getProductRelatedAttachments()
    public function getnewicon($type)
    {
        $baseurl =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $connection = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
        ->getConnection('core_read');
        $productsTable = $connection->getTableName('productattachments_extensions');
        $select = $conn->select()->from(['f' => $productsTable])
        ->where('f.status = 1');
        $result = $conn->fetchAll($select);
        foreach ($result as $value) {
            $ext_arr[] = strtolower($value['type']);
            $icons_arr[] = $baseurl.$value['icon'];
        }
        if (!isset($ext_arr)) {
            $ext_arr[] ='';
            $icons_arr[] = '';
        }
        $key = array_search($type, $ext_arr);
        if ($key !== false) {
            return '<img src="' . $icons_arr[$key].'" alt="' . $type . '" />';
        } else {
            return 0;
        }
    }
    public function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product;
    }//end getProduct()
    public function getCategory()
    {
        $category = $this->_coreRegistry->registry('current_category');
        return $category;
    }
    public function getCustomerGroupId()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerGroupId();
    }//end getCustomerGroupId()
    public function getSkinUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).'productattachments/'.$url;
    }//end getSkinUrl()
}//end class
