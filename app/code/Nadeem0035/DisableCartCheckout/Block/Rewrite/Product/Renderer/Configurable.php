<?php

namespace Nadeem0035\DisableCartCheckout\Block\Rewrite\Product\Renderer;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{


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