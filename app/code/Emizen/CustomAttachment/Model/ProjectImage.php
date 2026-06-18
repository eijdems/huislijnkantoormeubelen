<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Model;

use Magento\Framework\Model\AbstractModel;

class ProjectImage extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Emizen\CustomAttachment\Model\ResourceModel\ProjectImage::class);
    }
}
