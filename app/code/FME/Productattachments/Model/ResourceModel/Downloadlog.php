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
namespace FME\Productattachments\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

class Downloadlog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    protected $_objectManager;
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }

    public function _construct()
    {
        $this->_init('productattachments_downloadlogs', 'download_id');
    }
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
    }
    public function createDownloadLog($data)
    {
       
        $attachmentsTable = $this->getTable('productattachments_downloadlogs');
        $db = $this->getConnection();
        try {
            $db->beginTransaction();
            $db->exec("INSERT INTO " . $attachmentsTable . " (attachmentid, storeid, storename, downloadpage, created_time) VALUES ('".$data['attachmentid']."','".$data['storeid']."','".$data['storename']."','".$data['downloadpage']."','".date('y-m-d h:i:s')."')");
            $db->commit();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $db->rollBack();
            throw new LocalizedException($e);
        }
    }
}
