<?php


namespace FME\Productattachments\Model\System;

class AttachmentPosition extends \Magento\Framework\ObjectManager\ObjectManager
{


    protected $_objectManager;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config
    ) {
        parent::__construct($factory, $config);
        $this->_objectManager = $objectManager;
    }

    public function toOptionArray()
    {
        return [
            [
                'label' => __('Top'),
                'value' => 'top'
            ],
            [
                'label' => __('Bottom'),
                'value' => 'bottom'
            ]
        ];
    }
}
