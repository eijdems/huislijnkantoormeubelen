<?php


namespace Nadeem0035\DisableCartCheckout\Plugin;

use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product;

/**
 * Class IsSalablePlugin
 */
class IsSalable
{
    const DISABLE__CART_CHECKOUT = 'nadeem0035/settings/disable_cart_checkout';
    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * HTTP Context
     * Customer session is not initialized yet
     *
     * @var Context
     */
    protected $context;

    protected $product;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        Product $product
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
        $this->product = $product;
    }

    public function afterIsSalable(\Magento\Catalog\Model\Product $subject, $result)
    {
        $product = $this->product;

        if($subject->getData('type_id') != "downloadable"){
            $scope = ScopeInterface::SCOPE_STORE;
            if ($this->scopeConfig->getValue(self::DISABLE__CART_CHECKOUT, $scope)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
