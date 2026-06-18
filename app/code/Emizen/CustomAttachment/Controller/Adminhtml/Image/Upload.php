<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Controller\Adminhtml\Image;

use Emizen\CustomAttachment\Model\ImageUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Uploads a single Project Image and returns its metadata as JSON.
 */
class Upload extends Action
{
    /**
     * Reuse FME Product Attachments ACL so existing attachment editors keep access.
     */
    public const ADMIN_RESOURCE = 'FME_Productattachments::fmeextensions_productattachments_items';

    /**
     * POST field name the UI fileUploader posts under (matches the form field dataScope).
     */
    private const FILE_ID = 'project_images';

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir(self::FILE_ID);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
