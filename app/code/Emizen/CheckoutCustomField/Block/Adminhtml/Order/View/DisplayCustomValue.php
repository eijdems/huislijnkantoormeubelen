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
    public function getReferenceNumber(){
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderInterface->load($orderId);

        return $order->getReferenceNumber();
    }
    public function getCustomFile()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderInterface->load($orderId);

        $customFiles = $order->getCustomFile(); // Retrieve JSON-encoded filenames

        if ($customFiles) {
            // Decode the JSON string into an array
            $fileNames = json_decode($customFiles, true);

            // Construct full paths if needed
            $basePath = 'https://huislijnkantoormeubelen.ezxdemo.com/media/upload/';
            $fullPaths = array_map(function ($fileName) use ($basePath) {
                return $basePath . $fileName;
            }, $fileNames);

            return $fullPaths; // Return an array of full paths
        }

        return [];
    }
}