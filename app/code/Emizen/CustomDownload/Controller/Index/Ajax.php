<?php

namespace Emizen\CustomDownload\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use FME\Productattachments\Helper\Data as FmeHelper;

class Ajax extends Action
{
    protected $resultRawFactory;
    protected $eavConfig;
    protected $layout;
    protected $productCollectionFactory;
    protected $categoryFactory;
    protected $attachmentHelper;
    

    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        EavConfig $eavConfig,
        LayoutInterface $layout,
        ProductCollectionFactory $productCollectionFactory,
        CategoryFactory $categoryFactory,
        FmeHelper $attachmentHelper
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->eavConfig = $eavConfig;
        $this->layout = $layout;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->attachmentHelper = $attachmentHelper;
        
        parent::__construct($context);
    }

    public function execute()
    {
        $filter = $this->getRequest()->getParam('filter');
        $content = '';
        if ($filter == 'afbeeldingen111' || $filter == 'test1' || $filter == 'test11' || $filter == 'test2') {
            $selectedModels = $this->getRequest()->getParam('afbeeldingen_model', []);
            $modelsSelected = $this->getRequest()->getParam('models_selected');
            $categoryIds = $this->getRequest()->getParam('category_id', 62);
            $catIds = $this->getRequest()->getParam('catid', 62);

            //var_dump($categoryIds);
            // Load attribute options for afbeeldingen_model
            $attribute = $this->eavConfig->getAttribute('catalog_product', 'afbeeldingen_model');
            $options = $attribute->getSource()->getAllOptions();

            // Load products from category ID 62
            $categoryId = $categoryIds;
            
            
            $category = $this->_objectManager->create(\Magento\Catalog\Model\Category::class)->load($categoryId);

            // Create product collection
            $collection = $category->getProductCollection()
                ->addAttributeToSelect(['name', 'image', 'price', 'afbeeldingen_model'])
                ->addAttributeToFilter('visibility', ['neq' => 1]) // Exclude Not Visible Individually
                ->addAttributeToFilter('status', 1); // Only enabled

            // Apply the filter for selected models if any
            if (!empty($selectedModels)) {
                $category = $this->_objectManager->create(\Magento\Catalog\Model\Category::class)->load($catIds);

                // Create product collection
                $collection = $category->getProductCollection()
                ->addAttributeToSelect(['name', 'image', 'price', 'afbeeldingen_model'])
                ->addAttributeToFilter('visibility', ['neq' => 1]) // Exclude Not Visible Individually
                ->addAttributeToFilter('status', 1); // Only enabled
                $collection->addAttributeToFilter('afbeeldingen_model', ['in' => $selectedModels]);
                // Create block for rendering the product grid
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen_products.phtml');
                $block->setData('product_collection', $collection);
                $block->setData('categoryId1', $catIds);
                
            }elseif($modelsSelected){
                $category = $this->_objectManager->create(\Magento\Catalog\Model\Category::class)->load($catIds);

                // Create product collection
                $collection = $category->getProductCollection()
                ->addAttributeToSelect(['name', 'image', 'price', 'afbeeldingen_model'])
                ->addAttributeToFilter('visibility', ['neq' => 1]) // Exclude Not Visible Individually
                ->addAttributeToFilter('status', 1);
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen_products.phtml');
                $block->setData('product_collection', $collection);
                $block->setData('categoryId1', $catIds);
            }else{
                $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
                $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen.phtml');
                $block->setData('afbeeldingen_model_options', $options);
                $block->setData('product_collection', $collection);
                $block->setData('categoryId1', $categoryIds);
                $block->setData('dfilter', $filter);
            }
            $content = $block->toHtml();
        } 
        elseif (isset($filter) && filter_var($filter, FILTER_VALIDATE_INT) !== false) {
            // Valid subcategory ID
            $filterId = (int) $filter;

            // Get attachments by category ID using the helper
            $attachments = $this->attachmentHelper->getProductAttachments($filterId);

            // Create a block and set the appropriate template and data
            $block = $this->layout->createBlock(\Magento\Framework\View\Element\Template::class);
            $block->setTemplate('Emizen_CustomDownload::filters/afbeeldingen_subs.phtml');
            $block->setData('attachments', $attachments);
            $block->setData('categoryId1', $filterId);

            // Render the block (if required here)
            echo $block->toHtml();
        }

        else {
            // Handle other filters
            $templateMap = [
                'prijslijsten' => 'Emizen_CustomDownload::filters/prijslijsten.phtml',
                'materialen'   => 'Emizen_CustomDownload::filters/materialen.phtml',
                'nieuwsbrieven'=> 'Emizen_CustomDownload::filters/nieuwsbrieven.phtml',
                'dwg' => 'Emizen_CustomDownload::filters/dwg.phtml',
                'marketingpakketten' => 'Emizen_CustomDownload::filters/marketingpakketten.phtml',
                'afbeeldingen' => 'Emizen_CustomDownload::filters/afbeeldingen.phtml',
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
