<?php

namespace Emizen\CustomDownload\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;

class Ajax extends Action
{
    protected $resultRawFactory;
    protected $eavConfig;
    protected $layout;
    protected $productCollectionFactory;
    protected $categoryFactory;

    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        EavConfig $eavConfig,
        LayoutInterface $layout,
        ProductCollectionFactory $productCollectionFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->eavConfig = $eavConfig;
        $this->layout = $layout;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $filter = $this->getRequest()->getParam('filter');
        $content = '';

        if ($filter === 'afbeeldingen') {
            $selectedModels = $this->getRequest()->getParam('afbeeldingen_model', []);
            $modelsSelected = $this->getRequest()->getParam('models_selected');
            // Load attribute options for afbeeldingen_model
            $attribute = $this->eavConfig->getAttribute('catalog_product', 'afbeeldingen_model');
            $options = $attribute->getSource()->getAllOptions();

            // Load products from category ID 62
            $categoryId = 62;
            $category = $this->_objectManager->create(\Magento\Catalog\Model\Category::class)->load($categoryId);

            // Create product collection
            $collection = $category->getProductCollection()
                ->addAttributeToSelect(['name', 'image', 'price', 'afbeeldingen_model'])
                ->addAttributeToFilter('visibility', ['neq' => 1]) // Exclude Not Visible Individually
                ->addAttributeToFilter('status', 1); // Only enabled

            // Apply the filter for selected models if any
            if (!empty($selectedModels)) {
                $collection->addAttributeToFilter('afbeeldingen_model', ['in' => $selectedModels]);
                // Create block for rendering the product grid
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen_products.phtml');
                //$block->setData('afbeeldingen_model_options', $options);
                $block->setData('product_collection', $collection);
            }elseif($modelsSelected){
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen_products.phtml');
                //$block->setData('afbeeldingen_model_options', $options);
                $block->setData('product_collection', $collection);
            }else{
                // Create block for rendering the product grid
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen.phtml');
                $block->setData('afbeeldingen_model_options', $options);
                $block->setData('product_collection', $collection);
            }
            //var_dump($selectedModels);

            // Get HTML content for the product grid
            $content = $block->toHtml();
        } else {
            // Handle other filters
            $templateMap = [
                'prijslijsten' => 'Emizen_CustomDownload::filters/prijslijsten.phtml',
                'materialen'   => 'Emizen_CustomDownload::filters/materialen.phtml',
                'nieuwsbrieven'=> 'Emizen_CustomDownload::filters/nieuwsbrieven.phtml',
                'dwg' => 'Emizen_CustomDownload::filters/dwg.phtml',
                'marketingpakketten' => 'Emizen_CustomDownload::filters/marketingpakketten.phtml',
            ];

            if (isset($templateMap[$filter])) {
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate($templateMap[$filter]);
                $content = $block->toHtml();
            } else {
                $content = '<p>No content available for this filter.</p>';
            }
        }

        // Return the filtered content
        return $this->resultRawFactory->create()->setContents($content);
    }
}
