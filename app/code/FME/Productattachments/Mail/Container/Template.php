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
namespace FME\Productattachments\Mail\Container;

class Template extends \Magento\Sales\Model\Order\Email\Container\Template
{
    /**
     * @var array
     */
    protected $pdfAttach;

    /**
     * @var array
     */
    protected $imageAttach;

    public function setPdfList(array $pdfList)
    {
        $this->pdfAttach = $pdfList;
    }

    public function getPdfList()
    {
       
        return $this->pdfAttach;
    }

    public function setImageList(array $imageList)
    {
       
        $this->imageAttach = $imageList;
    }

    public function getImageList()
    {
        
        return $this->imageAttach;
    }
}
