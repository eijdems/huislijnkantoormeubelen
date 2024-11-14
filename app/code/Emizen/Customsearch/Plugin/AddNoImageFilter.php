<?php

namespace Emizen\Customsearch\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class AddNoImageFilter
{
    protected $logger;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeAddAttributeToSelect(ProductCollection $subject, $attribute)
    {
         if ($attribute === 'media_gallery') {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->getSelect()
                ->joinLeft(
                    ['gallery_table' => $collection->getTable('catalog_product_entity_media_gallery_value_to_entity')],
                    'e.entity_id = gallery_table.entity_id',
                    []
                )
                ->where('gallery_table.value_id IS NULL');
        }
        return [$attribute];
    }
}
