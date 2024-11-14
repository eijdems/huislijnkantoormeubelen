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
namespace FME\Productattachments\Plugin;

use \Magento\Sales\Block\Items\AbstractItems;

/**
 * This plugin add attachment to html elements.
 */
class AroundItemHtml
{
    /**
     * @param Options $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetItemHtml(AbstractItems $subject, \Closure $proceed)
    {
        $result = $proceed();
        return $result;
    }
}
