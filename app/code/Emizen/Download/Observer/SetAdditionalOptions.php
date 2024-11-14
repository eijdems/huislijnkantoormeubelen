<?php
namespace Emizen\Download\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class SetAdditionalOptions implements ObserverInterface
{
    protected $_request;    
    public function __construct(
        RequestInterface $request, 
        Json $serializer = null
        ) 
    {
        $this->_request = $request;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Check and set information according to your need
        $post = $this->getRequest()->getPostValue();
        echo $article = is($post, 'article', '');
die("ZD");
        $product = $observer->getProduct();                    
        echo $this->_request->getFullActionName();
        echo "<pre>";
        print_r($product->debug());die;
        if ($this->_request->getFullActionName() == 'productconfigurator_product_addtocart') { //checking when product is adding to cart
            $product = $observer->getProduct();
            $additionalOptions = [];
            $additionalOptions[] = array(
                'label' => "Some Label", //Custom option label
                'value' => "Your Information", //Custom option value
            );                        
            $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
        }
    }

}