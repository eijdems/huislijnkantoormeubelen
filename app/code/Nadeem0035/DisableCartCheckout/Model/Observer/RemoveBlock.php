<?php

namespace Nadeem0035\DisableCartCheckout\Model\Observer;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveBlock implements ObserverInterface
{


    const DISABLE__CART_CHECKOUT = 'nadeem0035/settings/disable_cart_checkout';
    const MINICART_BLOCK = 'minicart';
    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }


    public function execute(Observer $observer)
    {
        $layout = $observer->getLayout();
        $block = $layout->getBlock(self::MINICART_BLOCK);
        $scope = ScopeInterface::SCOPE_STORE;

        if ($block) {
            $remove = $this->scopeConfig->getValue(self::DISABLE__CART_CHECKOUT, $scope);
            if ($remove) {
                $layout->unsetElement(self::MINICART_BLOCK);
            }
        }
    }
}