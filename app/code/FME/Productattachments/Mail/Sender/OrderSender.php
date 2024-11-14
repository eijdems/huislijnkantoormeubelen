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
namespace FME\Productattachments\Mail\Sender;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * @var \Fooman\EmailAttachments\Model\AttachmentContainerInterface
     */
    protected $templateContainer;
    protected $customHeper;

    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \FME\Productattachments\Helper\Data $customHeper,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->templateContainer = $templateContainer;
        $this->customHeper = $customHeper;
        parent::__construct(
            $this->templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $orderResource,
            $globalConfig,
            $eventManager
        );
    }

    public function send(\Magento\Sales\Model\Order $order, $forceSyncMode = true)
    {
        $items = $order->getAllItems();
        $IncrementId = $order->getIncrementId();
       
        $arrayMail=$this->customHeper->attachmentEmail();
        if ($arrayMail['enable'] == 1) {
            $orderstatus=explode(',', $arrayMail['orderstatus']);
            if (empty($orderstatus) || in_array($order->getStatus(), $orderstatus)) {
                $imageData = [];
                $pdfData = [];
                $attachments = [];
                foreach ($items as $item) {
                    $attachments[] = $this->customHeper->getProductAttachmentsById($item->getProductId());
                }
                foreach ($attachments as $key => $value) {
                    if (!empty($value)) {
                        foreach ($value as $key1 => $value1) {
                            if ($value1['filename'] != '') {
                                if ($value1['file_type'] == 'jpg' || $value1['file_type'] == 'jpeg' || $value1['file_type'] == 'gif' || $value1['file_type'] == 'png') {
                                    $imageData[] = $value1['filename'];
                                } else {
                                    $pdfData[] = $value1['filename'];
                                }
                            }
                        }
                    }
                }

                if (count($pdfData) > 0) {
                        $this->templateContainer->setPdfList($pdfData);
                }

                if (count($imageData) > 0) {
                        $this->templateContainer->setImageList($imageData);
                }
            }
        }
        return parent::send($order, $forceSyncMode);
    }
}
