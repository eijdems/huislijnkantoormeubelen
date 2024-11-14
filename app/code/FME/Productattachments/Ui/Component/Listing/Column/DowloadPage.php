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
namespace FME\Productattachments\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class DowloadPage extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image   $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array                           $components
     * @param array                           $data
     */
    
    protected $productattachments;
    protected $_storeManager;
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder    = $urlBuilder;
        $this->_storeManager = $storeManager;
    }//end __construct()
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $item) {
                if ($item['downloadpage'] == 'catalog_category_view') {
                    $dataSource['data']['items'][$key]['downloadpage'] = 'Category Page';
                } elseif ($item['downloadpage'] == 'productattachments_index_attachments') {
                    $dataSource['data']['items'][$key]['downloadpage'] = 'Attachments Page';
                } elseif ($item['downloadpage'] == 'cms_page_view') {
                    $dataSource['data']['items'][$key]['downloadpage'] = 'CMS Page';
                } elseif ($item['downloadpage'] == 'catalog_product_view') {
                    $dataSource['data']['items'][$key]['downloadpage'] = 'Product Page';
                }
            }
        }

        return $dataSource;
    }//end prepareDataSource()
}//end class
