<?php
namespace Emizen\CustomDownload\Block;

use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\BlockFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class Sidebar extends Template
{
    protected $_categoryCollectionFactory;
    protected $_blockFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }

    public function getFilterTabs()
    {
        return [
            'Prijslijsten' => 'prijslijsten',
            'DWG Bestanden' => 'dwg_bestanden',
            'Materialen' => 'materialen',
            'Afbeeldingen' => 'afbeeldingen',
            'Nieuwsbrieven' => 'nieuwsbrieven'
        ];
    }

    public function getCategoryList()
    {
        $categoryCollection = $this->_categoryCollectionFactory->create();
        $categoryCollection->addAttributeToSelect('*')->addFieldToFilter('entity_id', 62); // Category ID 62
        return $categoryCollection;
    }

    public function getCmsBlockContent($blockId)
    {
        try {
            $cmsBlock = $this->_blockFactory->create()->load($blockId);
            return $cmsBlock->getContent();
        } catch (\Exception $e) {
            return 'CMS block not found.';
        }
    }
}
