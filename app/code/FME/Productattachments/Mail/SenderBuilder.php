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
namespace FME\Productattachments\Mail;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{

    /**
     * Prepare and send email message
     *
     * @return void
     */
    public function send()
    {
        $ImageList = $this->templateContainer->getImageList();
        $PdfList = $this->templateContainer->getPdfList();

        if (is_array($ImageList)) {
            foreach ($ImageList as $key => $data) {
                $this->transportBuilder->addImageAttachment(file_get_contents($data), $data);
            }
        }

        if (is_array($PdfList)) {
            foreach ($PdfList as $key => $data) {
                $this->transportBuilder->addPdfAttachment(file_get_contents($data), $data);
            }
        }
        parent::send();
    }
}
