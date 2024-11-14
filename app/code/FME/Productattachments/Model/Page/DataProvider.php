<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category FME
 * @package FME_Productattachments
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productattachments\Model\Page;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
//use FME\Productattachments\Model\Page\CustomLayoutManagerInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;
    protected $pool;
    protected $meta;
    protected $_objectManager;
    protected $customLayoutManager;    

    
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->pool         = $pool;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        $meta = parent::getMeta();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $version = $this->_objectManager->create('Magento\Framework\App\ProductMetadataInterface');

        if (version_compare($version->getVersion(), '2.3.4', '>=')) {
            $this->customLayoutManager = $this->_objectManager->create('Magento\Cms\Model\Page\CustomLayoutManagerInterface');
            $options = [['label' => 'No update', 'value' => '_no_update_']];
            foreach ($this->collection->getItems() as $key => $value) {
                if ($value->getCustomLayoutUpdateXml() || $value->getLayoutUpdateXml()) {
                    $options[] = ['label' => 'Use existing layout update XML', 'value' => '_existing_'];
                }
                foreach ($this->customLayoutManager->fetchAvailableFiles($value) as $layoutFile) {
                    $options[] = ['label' => $layoutFile, 'value' => $layoutFile];
                }
            }
            $customLayoutMeta = [
                'design' => [
                    'children' => [
                        'custom_layout_update_select' => [
                            'arguments' => [
                                'data' => ['options' => $options]
                            ]
                        ]
                    ]
                ]
            ];
            $meta = array_merge_recursive($meta, $customLayoutMeta);
        } else {
            $this->auth = $this->_objectManager->create('Magento\Framework\AuthorizationInterface');
            if (!$this->auth->isAllowed('Magento_Cms::save_design')) {
                $designMeta = [
                    'design' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'disabled' => true
                                ]
                            ]
                        ]
                    ],
                    'custom_design_update' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'disabled' => true
                                ]
                            ]
                        ]
                    ]
                ];
                $meta = array_merge_recursive($meta, $designMeta);
            }
        }
        
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $page \Magento\Cms\Model\Page */
        foreach ($items as $page) {
            $this->loadedData[$page->getId()] = $page->getData();
        }

        $data = $this->dataPersistor->get('cms_page');
        if (!empty($data)) {
            $page = $this->collection->getNewEmptyItem();
            $page->setData($data);
            $this->loadedData[$page->getId()] = $page->getData();
            $this->dataPersistor->clear('cms_page');
        }
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            if (!empty($this->loadedData)) {
                $this->loadedData = $modifier->modifyData($this->loadedData);
            }
        }
        return $this->loadedData;
    }
}
