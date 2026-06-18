<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Plugin;

use Emizen\CustomAttachment\Model\ResourceModel\ProjectImage as ProjectImageResource;
use FME\Productattachments\Model\ResourceModel\Productattachments as AttachmentResource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;
use Psr\Log\LoggerInterface;

/**
 * Persists the "project_images" fileUploader payload (carried on the attachment model by
 * FME's blind setData()) into our own relation table, after the attachment itself is saved.
 */
class SaveProjectImagesPlugin
{
    /**
     * Media-relative directory where Project Images live.
     */
    private const BASE_PATH = 'emizen/project_images';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResourceConnection $resourceConnection
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * @param AttachmentResource $subject
     * @param AttachmentResource $result
     * @param AbstractModel $object
     * @return AttachmentResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        AttachmentResource $subject,
        $result,
        AbstractModel $object
    ) {
        // Only act on the attachment admin form submit. Other saves (e.g. the download
        // counter) must not touch project images. The form always posts the required
        // "title" and "cat_id" fields; an emptied uploader omits its own key, so a
        // missing "project_images" on a real form submit means "all images removed".
        if (!$this->isAttachmentFormSave()) {
            return $result;
        }

        $attachmentId = (int)$object->getId();
        if (!$attachmentId) {
            return $result;
        }

        $images = $object->getData('project_images');
        $rows = $this->normalizeImages(is_array($images) ? $images : []);

        try {
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName(ProjectImageResource::TABLE_NAME);

            // Full re-sync for this attachment: removed images disappear, current ones persist in order.
            $connection->delete($table, ['attachment_id = ?' => $attachmentId]);

            $position = 0;
            foreach ($rows as $relativePath) {
                $connection->insert($table, [
                    'attachment_id' => $attachmentId,
                    'image' => $relativePath,
                    'position' => $position++,
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $result;
    }

    /**
     * Whether the current save originates from the attachment admin edit form.
     *
     * @return bool
     */
    private function isAttachmentFormSave(): bool
    {
        $post = $this->request->getPostValue();
        return is_array($post) && array_key_exists('title', $post) && array_key_exists('cat_id', $post);
    }

    /**
     * Convert the raw fileUploader payload into a de-duplicated list of media-relative paths.
     *
     * @param array $images
     * @return string[]
     */
    private function normalizeImages(array $images): array
    {
        $paths = [];
        foreach ($images as $image) {
            if (!is_array($image)) {
                continue;
            }
            $name = $image['file'] ?? ($image['name'] ?? null);
            if (!$name) {
                continue;
            }
            // Store as <base>/<basename> regardless of whether the payload is freshly uploaded
            // (bare filename) or round-tripped from the form (already prefixed).
            $relativePath = self::BASE_PATH . '/' . basename((string)$name);
            $paths[$relativePath] = $relativePath;
        }

        return array_values($paths);
    }
}
