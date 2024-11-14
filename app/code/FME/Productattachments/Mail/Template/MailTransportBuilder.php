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
namespace FME\Productattachments\Mail\Template;

class MailTransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function addPdfAttachment($fileContent, $filename)
    {
        if ($fileContent) {
            $this->message->createAttachment(
                $fileContent,
                'application/pdf',
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $filename
            );

            return $this;
        }
    }

    public function addImageAttachment($fileContent, $filename)
    {
        if ($fileContent) {
            $this->message->createAttachment(
                $fileContent,
                \Zend_Mime::TYPE_OCTETSTREAM,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $filename
            );

            return $this;
        }
    }
}
