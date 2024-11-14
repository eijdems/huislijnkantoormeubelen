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

namespace FME\Productattachments\Block\Adminhtml\Extensions\Edit;

use Magento\Backend\Block\Widget\Context;
// use FME\News\Api\NewsRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;


    /**
     * @param Context                  $context
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
        // $this->blockRepository = $blockRepository;
    }//end __construct()


    /**
     * Return CMS block ID
     *
     * @return integer|null
     */
    public function getBlockId()
    {
        return null;
    }//end getBlockId()

    /**
     * Generate url by route and parameters
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }//end getUrl()
}//end class
