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
declare(strict_types=1);

namespace FME\Productattachments\Setup\Patch\Data;

use FME\Productattachments\Model\ProductcatsFactory;
use FME\Productattachments\Model\ProductattachmentsFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class DataPatch implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $productcatsFactory;
    private $resourceModel;

    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $productattachmentsFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductcatsFactory $productcatsFactory,
        ProductattachmentsFactory $productattachmentsFactory,
        \FME\Productattachments\Model\ResourceModel\Productattachments $resourceModel
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productcatsFactory = $productcatsFactory;
        $this->productattachmentsFactory = $productattachmentsFactory;
        $this->resourceModel = $resourceModel;
    }
    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $tableCats = $this->moduleDataSetup->getTable('productattachments_cats');
        $tableIcons = $this->moduleDataSetup->getTable('productattachments_extensions');
        if ($this->moduleDataSetup->getConnection()->isTableExists($tableCats) == true) {
            $data = [
                'category_name'      => 'Default Category',
                'category_url_key'   => 'Default_Category',
                'parent_category_id' => 0,
                'level'              => 0,
                'path'               => 1,
                'category_store_ids' => [0],
                'created_at'         => date('Y-m-d H:i:s'),
                'is_root'            => 1,
                'status'             => 1,
            ];
            // smart approach for avoiding conflict
            $isExistDefault = $this->createProductCats()->load('Default_Category', 'category_url_key');
            $lastId         = 0;
            if (!$isExistDefault->getId()) {
                $lastId = $this->createProductcats()->setData($data)->save()->getId();
            } else {
                $lastId = $isExistDefault->getId();
            }
            $this->_updateProductcats($lastId);
            $this->_updateProductattachments($lastId);
        }
        //upgrade data code
        
        $check = $this->resourceModel->checkDefaultCategoryEntry();
        if ( $check ) {
           
            $tableCatsStore = $this->moduleDataSetup->getTable('productattachments_category_store');
            $isExistDefault = $this->createProductCats()->load('Default_Category', 'category_url_key');
            $lastId         = $isExistDefault->getId();
            if ($this->moduleDataSetup->getConnection()->isTableExists($tableCatsStore) == true && $lastId) {
                $store_data = [
                    'category_id'=> $lastId,
                    'store_id'   => 0
                ];
                $this->moduleDataSetup->getConnection()->insert($tableCatsStore, $store_data);
            }

        }


        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }
     /**
      * Create productcats
      *
      * @return productcats
      */
     public function createProductcats()
     {
        return $this->productcatsFactory->create();
    }//end createProductcats()

     /**
      * Create productcats
      *
      * @return productcats
      */
     public function createProductattachments()
     {
        return $this->productattachmentsFactory->create();
    }//end createProductattachments()

    protected function _updateProductcats($lastId)
    {
        $collection = $this->createProductcats()->getCollection()->addFieldToFilter('parent_category_id', 0)->addFieldToFilter('category_id', ['neq' => $lastId]);

        foreach ($collection as $item) {
            $this->createProductcats()->setId($item->getId())->setParentCategoryId($lastId)->setPath($lastId.'/'.$item->getId())->setLevel(1)->save();
        }
    }
    protected function _updateProductattachments($lastId)
    {
        $collection = $this->createProductattachments()->getCollection()->addFieldToFilter('cat_id', 0);

        foreach ($collection as $item) {
            $this->createProductattachments()->setId($item->getId())->setCatId($lastId)->save();
        }
    }//end _updateProductattachments()
}
