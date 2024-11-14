<?php
namespace FME\Productattachments\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Productattachments implements ArgumentInterface
{
    public $objectManager;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
