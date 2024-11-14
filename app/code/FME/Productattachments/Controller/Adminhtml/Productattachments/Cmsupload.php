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
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_Productattachments
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productattachments\Controller\Adminhtml\Productattachments;

use Magento\Framework\App\Filesystem\DirectoryList;
use FME\Productattachments\Model\Productattachments as ProductattachmentsModel;

class Cmsupload extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::cms';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    protected $productattachmentsModel;
    protected $_customergroup;
    protected $_objectManager;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProductattachmentsModel $productattachmentsModel,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Customer\Model\Group $customer_group
    ) {
        parent::__construct($context);
        $this->_customergroup = $customer_group;
        $this->_objectManager       = $objectManager;
        $this->productattachmentsModel = $productattachmentsModel;
        $this->resultRawFactory = $resultRawFactory;
    }

    protected function _isAllowed()
    {
        return true;
    }
    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            //////// get extensions list
            $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');
            $connection = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
            
            $productsTable = $connection->getTableName('productattachments_extensions');
           
            $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.status = 1');
            $result = $conn->fetchAll($select);
       
            $ext_arr = ['jpg', 'jpeg', 'gif', 'png', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'zip', 'ppt', 'pptx', 'flv', 'mp3', 'mp4', 'csv', 'html', 'bmp', 'txt', 'rtf', 'psd','dvi', 'ods'];
            foreach ($result as $value) {
                 $ext_arr[] = $value['type'];
            }
      
            if (!isset($ext_arr)) {
                 $ext_arr[] ='';
            }
            ///////////////////////////////////////// get extensions list
            $params =  $this->getRequest()->getPostValue();
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'file']
            );
            $uploader->setAllowedExtensions($ext_arr);
            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $config = $this->_objectManager->get('FME\Productattachments\Model\Media\Config');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath()));

            $this->_eventManager->dispatch(
                'catalog_product_gallery_upload_image_after',
                ['result' => $result, 'action' => $this]
            );
            unset($result['tmp_name']);
            unset($result['path']);
            $result['cms_id'] = $params['cms_id'];
            $result['label'] = $result['file'];
            $result['url'] = $this->_objectManager->get('FME\Productattachments\Model\Media\Config')
                ->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';

            $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
            $model = $this->productattachmentsModel;

             $fileconfig = $this->_objectManager->create('FME\Productattachments\Model\Image\Fileicon');
             $filepath = $baseScmsMediaURL.'productattachments/files'.$result['label'];
             $fileconfig->Fileicon($filepath);
    
            $data['file_icon'] = $fileconfig->displayIcon();
            $data['file_type'] = $fileconfig->getType();
            $data['file_size'] = $fileconfig->getSize();
            $data['cmspage_ids'] = (array)$result['cms_id'];
            $data['product_id'] = (array)null;
            $data['title'] = $result['name'];
            $data['status'] = 1;
            $data['downloads'] = 0;
             $data['filename'] = 'productattachments/files'.$result['label'];
             $data['block_position'] = 'additional,other';
             $data['limit_downloads'] = 0;
             $data['download_link'] = $result['url'];
             $data['cat_id'] = 1;
             $data['store_id'] = (array)0;
             $groups_array = [];
             $allGroups    = $this->_customergroup->getCollection()->toOptionHash();
            foreach ($allGroups as $key => $allGroup) {
                $groups_array[] = $key;
            }

            $cgroup = implode(',', $groups_array);
            $data['customer_group_id'] = $cgroup;
             $model->setData($data);
            if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                  $model->setCreatedTime(date('y-m-d h:i:s'))
                          ->setUpdateTime(date('y-m-d h:i:s'));
            }
              $model->save();
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}
