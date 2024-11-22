<?php
namespace Emizen\CheckoutCustomField\Block\Adminhtml\Order\View;

use Magento\Sales\Api\Data\OrderInterface;
class DisplayCustomValue extends \Magento\Backend\Block\Template
{
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        OrderInterface $orderInterface,
        array $data = []
    ) {
        $this->orderInterface = $orderInterface;
        parent::__construct($context, $data);
    }
    public function getAgree(){
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderInterface->load($orderId);

        return $order->getAgree();
    }
}