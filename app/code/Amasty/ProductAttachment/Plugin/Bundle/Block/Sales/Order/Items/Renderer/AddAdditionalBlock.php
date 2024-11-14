<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Attachments Base for Magento 2
 */

namespace Amasty\ProductAttachment\Plugin\Bundle\Block\Sales\Order\Items\Renderer;

use Magento\Bundle\Block\Sales\Order\Items\Renderer;

class AddAdditionalBlock
{
    public function afterToHtml(Renderer $subject, string $html): string
    {
        $addInfoBlock = $subject->getProductAdditionalInformationBlock();
        if ($addInfoBlock && $item = $subject->getItem()) {
            $html .= $addInfoBlock->setItem($item)->toHtml();
        }

        return $html;
    }
}
