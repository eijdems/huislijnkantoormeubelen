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
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_Productattachments
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productattachments\Controller\Adminhtml\Extensions;

use FME\Productattachments\Controller\Adminhtml\AbstractMassStatus;

class MassStatus extends AbstractMassStatus
{
    public function execute()
    {
        $extensionsIds = $this->getRequest()->getParam('status');
        if (!is_array($extensionsIds)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($extensionsIds as $exten) {
                    $productcats = $this->_objectManager->get('FME\Productattachments\Model\Extensions')
                            ->load($exten)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %d record(s) were successfully updated', count($extensionsIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
