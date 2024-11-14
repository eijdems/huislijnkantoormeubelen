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
namespace FME\Productattachments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use FME\Productattachments\Model\Productattachments as ProductattachmentsModel;

class Cmssaveafter implements ObserverInterface
{

    protected $request;
    protected $productattachmentsModel;
    protected $_objectManager;

    protected $_resource;
    public function __construct(Context $context, ProductattachmentsModel $productattachmentsModel)
    {
        $this->_objectManager = $context->getObjectManager();
        $this->request = $context->getRequest();
        $this->productattachmentsModel = $productattachmentsModel;
        $this->_resource = ObjectManager::getInstance()->get('\Magento\Framework\App\ResourceConnection');
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $postData = $this->request->getPost();



        $page_id = $postData['page_id'];
        if (!isset($page_id) && $page_id=="") {
            return;
        }
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
        $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();





        $fileconfig = $this->_objectManager->create('FME\Productattachments\Model\Image\Fileicon');
        $dont_delete = [];


        if (isset($postData['page']['attachments'])) {
            foreach ($postData['page']['attachments']['dynamic_rows'] as $key => $value) {
                if (isset($value['attachment_id'])) {
                    $data['productattachments_id'] = $value['attachment_id'];
                    $model = $this->productattachmentsModel->load($value['attachment_id']);
                } else {
                    $data['productattachments_id'] = null;
                    $model = $this->productattachmentsModel;
                }
                if (isset($value['is_delete'])) {
                    if (isset($value['attachment_id'])) {
                        $model_del = $this->_objectManager->create('FME\Productattachments\Model\Productattachments');

                        $model_del->setId($value['attachment_id'])->delete();
                         unset($data);
                         unset($model_del);
                        continue;
                    } else {
                        $data['productattachments_id'] = null;
                        continue;
                    }
                }
                if (isset($value['filename'])) {
                    if ($value['filename'][0]['status'] == 'old') {
                        // $temp='';
                        $data['download_link'] = $model->getData()['download_link'];
                        $data['file_icon'] = $model->getData()['file_icon'];
                        $data['file_type'] = $model->getData()['file_type'];
                        $data['file_size'] = $model->getData()['file_size'];
                    } elseif ($value['filename'][0]['status'] == 'new') {
                        $filepath = $baseScmsMediaURL.'productattachments/files/'.$value['filename'][0]['name'];

                        $data['filename'] = 'productattachments/files/'.$value['filename'][0]['name'];

                        $fileconfig->Fileicon($filepath);
                        $data['download_link'] = $value['filename'][0]['url'];
                        $data['file_icon'] = $fileconfig->displayIcon();
                        $data['file_type'] = $fileconfig->getType();
                        $data['file_size'] = $fileconfig->getSize();
                    }
                }
                    $data['cmspage_ids'] = (array)$page_id;
                    $data['product_id'] = (array)null;
                    $data['title'] = $value['title'];
                    //$data['status'] = @$value['status'] ? @$value['status'] : 1;
                if (isset($value['status'])) {
                    if ($value['status']) {
                        $data['status'] = $value['status'];
                    } else {
                        $data['status'] = 1;
                    }
                }
                    $data['downloads'] = 0;
             
                     $data['block_position'] = 'additional,other';
                     $data['limit_downloads'] = 0;
            
                     $data['cat_id'] = 1;
                     $data['store_id'] = (array)0;

                if ($value['type'] == 'url') {
                    $data['link_url'] = $value['link_url'];
                    $data['file_icon'] = '';
                    $data['file_type'] = '';
                    $data['file_size'] = '';
                    $data['filename'] = '';
                }
                if (isset($value['customer_group'])) {
                    foreach ($value['customer_group'] as $key => $allGroup) {
                          $groups_array[] = $allGroup;
                    }
                    $cgroup = implode(',', $groups_array);
                    $data['customer_group_id'] = $cgroup;
                    unset($groups_array);
                } else {
                    $data['customer_group_id'] = null;
                }
                     $model->setData($data);
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                      $model->setCreatedTime(date('y-m-d h:i:s'))
                      ->setUpdateTime(date('y-m-d h:i:s'));
                }
                $model->save();
                $dont_delete[] = $model->getId(); //attachment_id
            }
        }
        $relatedAttachments = $this->productattachmentsModel->getRelatedCms($page_id);
        foreach ($relatedAttachments as $attachment) {
            if (in_array($attachment['productattachments_id'], $dont_delete)) {
                //let this id servive
            } else {
                $read = $this->_resource->getConnection('core_read');
                $myTable = $this->_resource->getTableName('productattachments_cms');
                $read->delete(
                    $myTable,
                    ['productattachments_id = ?' => $attachment['productattachments_id'], 'cms_id = ?' => $page_id]
                );
            }
        }
    }
}
