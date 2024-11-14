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

namespace FME\Productattachments\Model\Config\Source;

class SortOrder implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $groups_array = [];
        //alphabetical, date, size, downloads
            $groups_array[] = [
                               'value' => 0,
                               'label' => 'Default',
                              ];
            $groups_array[] = [
                               'value' => 1,
                               'label' => 'Alphabetical (title)',
                              ];
            $groups_array[] = [
                               'value' => 2,
                               'label' => 'Size (attachment)',
                              ];
            $groups_array[] = [
                               'value' => 3,
                               'label' => 'Date (created)',
                              ];
            $groups_array[] = [
                               'value' => 4,
                               'label' => 'Downloads',
                              ];
            return $groups_array;
    }//end toOptionArray()
}//end class
