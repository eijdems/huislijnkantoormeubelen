<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Model\ResourceModel\ProjectImage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Emizen\CustomAttachment\Model\ProjectImage::class,
            \Emizen\CustomAttachment\Model\ResourceModel\ProjectImage::class
        );
    }
}
