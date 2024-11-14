<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Plugin\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\View\Result\Page;

/**
 * TODO remove extends
 */
class CategoryViewAjax extends Ajax
{
    /**
     * @param Action $controller
     *
     * @return array
     */
    public function beforeExecute(Action $controller)
    {
        if ($this->isAjax($controller->getRequest())) {
            $this->getActionFlag()->set('', 'no-renderLayout', true);
        }

        return [];
    }

    /**
     * @param Action $controller
     * @param Page $page
     *
     * @return Raw|Page
     */
    public function afterExecute(Action $controller, $page)
    {
        if (!$this->isAjax($controller->getRequest())) {
            return $page;
        }

        $responseData = $this->isCounterRequest($controller->getRequest())
            ? $this->counterDataProvider->execute()
            : $this->getAjaxResponseData();

        return $this->prepareResponse($responseData);
    }
}
