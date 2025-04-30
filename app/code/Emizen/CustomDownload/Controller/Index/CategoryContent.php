<?php

namespace Emizen\CustomDownload\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Block\Product\ListProduct;

class CategoryContent extends Action
{
    protected $categoryFactory;
    protected $resultRawFactory;
    protected $productCollectionFactory;

    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        RawFactory $resultRawFactory,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

   public function execute()
    {
        $categoryId = (int)$this->getRequest()->getParam('cat');

        if (!$categoryId) {
            return $this->resultRawFactory->create()->setContents('<p>Category ID missing.</p>');
        }

        // Load category object
        $category = $this->categoryFactory->create()->load($categoryId);

        if (!$category->getId()) {
            return $this->resultRawFactory->create()->setContents('<p>Invalid category.</p>');
        }

        // Get category name and image URL
        $categoryName = $category->getName();
        $categoryImageUrl = $category->getImageUrl();

        // Create a product collection for the current category
        $collection = $this->productCollectionFactory->create()
            ->addCategoryFilter($category) // Filter products by category
            ->addAttributeToSelect('*')    // Select all attributes (you can customize this)
            ->setPageSize(10);            // Limit the number of products (optional)

        // Initialize the $html variable to store the output
        $html = ''; // Initialize the $html variable

        // Create product list HTML
        $productHtml = '';
        foreach ($collection as $product) {
            $productHtml .= '<div class="product-item">';
            
            // Get custom attribute 'afbeeldingen_model'
            $afbeeldingenModel = $product->getAfbeeldingenModel();  // Replace 'AfbeeldingenModel' with your actual attribute code
            if ($afbeeldingenModel) {
                $productHtml .= '<p><strong>Afbeeldingen Model:</strong> ' . $afbeeldingenModel . '</p>';
            }

            // Get image URL
            $imageUrl = $product->getImage(); // Get image path from the product
            $imageUrl = $product->getMediaConfig()->getMediaUrl($imageUrl); // Get the full URL of the image
            $productHtml .= '<h2>' . $product->getName() . '</h2>';
            if ($imageUrl) {
                $productHtml .= '<img src="' . $imageUrl . '" alt="' . $product->getName() . '" />';
            }
            $productHtml .= '</div>';
        }

        // Append the product list HTML
        if ($productHtml) {
            $html .= '<div class="product-list">';
            $html .= $productHtml;
            $html .= '</div>';
        } else {
            $html .= '<p>No products available in this category.</p>';
        }

        // Return raw HTML content
        return $this->resultRawFactory->create()->setContents($html);
    }


}
