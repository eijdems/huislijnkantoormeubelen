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
namespace FME\Productattachments\Model\Productattachments;

use FME\Productattachments\Model\ResourceModel\Productattachments\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $collection;
    public $_storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;
    protected $productattachments;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $blockCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $blockCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Productattachments\Model\Productattachments $Productattachments,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $blockCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_storeManager=$storeManager;
        $this->productattachments = $Productattachments;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $baseurl =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Magento\Cms\Model\Block $block */
        $attachId=null;
        foreach ($items as $block) {
            $attachId=$block->getId();
        }

        foreach ($this->productattachments->getEditCats($attachId) as $key => $value) {
            $cats[]=(string)$value['cats_id'];
        }
        foreach ($this->productattachments->getEditStore($attachId) as $key => $value) {
            $store[]=(string)$value['store_id'];
        }
        foreach ($items as $block) {
            if (isset($cats)) {
                $items[$block->getId()]['cats_id'] = $cats;
            }
            if (isset($store)) {
                $items[$block->getId()]['store_id'] = $store;
            }
            $this->loadedData[$block->getId()] = $block->getData();

             $temp = $block->getData();
                $img = [];
                $img[0]['name'] = $temp['filename'];
                $img[0]['url'] = $baseurl.$temp['filename'];
               $temp['filename'] = $img;
        }
        $data = $this->dataPersistor->get('productattachments');
        if (!empty($data)) {
            $block = $this->collection->getNewEmptyItem();
            $block->setData($data);
            $this->loadedData[$block->getId()] = $block->getData();
             
            $this->dataPersistor->clear('productattachments');
        }
        if (empty($this->loadedData)) {
            return $this->loadedData;
        } else {
            if ($block->getData('filename') != null) {
                $t2[$block->getId()] = $temp;
                return $t2;
            } else {
                return $this->loadedData;
            }
        }
    }
}
