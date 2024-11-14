<?php

namespace Nadeem0035\DisableCartCheckout\Block\Rewrite\Product\View\Type;

class Configurable extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{

    /**
     * Get Allowed Products
     *
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = [];
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach ($allProducts as $product) {
                $products[] = $product;
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    protected function getRendererTemplate()
    {
        return 'Nadeem0035_DisableCartCheckout::product/view/type/options/configurable.phtml';
    }

}