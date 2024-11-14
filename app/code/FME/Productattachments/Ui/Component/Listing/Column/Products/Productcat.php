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
namespace FME\Productattachments\Ui\Component\Listing\Column\Products;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Productcat
 */
class Productcat extends Column
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Sample\News\Model\Uploader
     */
    protected $urlBuilder;
    protected $_objectManager;
    protected $_catModel;


    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Sample\News\Model\Uploader $imageModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \FME\Productattachments\Model\Productcats $catModel,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_catModel = $catModel;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                 $catname = $this->_objectManager->get('FME\Productattachments\Model\Productcats')->load($item['cat_id']);
               
                 $item[$fieldName] = $catname->getCategory_name();
            }
        }
   
        return $dataSource;
    }
}
