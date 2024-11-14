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

class DowloadedAttachemnt extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';

    protected $productattachments;
    protected $_storeManager;
    protected $urlBuilder;
    /**
     * @param ContextInterface                $context
     * @param UiComponentFactory              $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image   $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array                           $components
     * @param array                           $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Productattachments\Model\Productattachments $productattachments,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productattachments = $productattachments;
        $this->urlBuilder    = $urlBuilder;
        $this->_storeManager = $storeManager;
    }//end __construct()
    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        $media_url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('attachmentid');
            foreach ($dataSource['data']['items'] as $key => $item) {
                $attach=$this->productattachments->getEditCats($item['attachmentid']);
                if (isset($attach[0]['title'])) {
                    $dataSource['data']['items'][$key]['attachmentid'] = $attach[0]['title'];
                }
            }
        }//end if
        return $dataSource;
    }//end prepareDataSource()
}//end class
