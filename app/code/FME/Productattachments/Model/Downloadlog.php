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
namespace FME\Productattachments\Model;

class Downloadlog extends \Magento\Framework\Model\AbstractModel
{
    /*
        * #@+
        * Page's Statuses
     */
    protected $_customergroup;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \FME\Productattachments\Model\ResourceModel\Downloadlog $resource_m, \FME\Productattachments\Model\ResourceModel\Downloadlog\Collection $resourceCollection,\Magento\Customer\Model\Group $customer_group
    )
    {
        $this->_customergroup = $customer_group;
        parent::__construct(
            $context,
            $registry,
            $resource_m,
            $resourceCollection
        );
    }//end __construct()
    public function _construct()
    {
        $this->_init('FME\Productattachments\Model\ResourceModel\Downloadlog');
    }//end _construct()
    public function createDownloadLogs($data)
    {
        return $this->_getResource()->createDownloadLog($data);
    }
}//end class
