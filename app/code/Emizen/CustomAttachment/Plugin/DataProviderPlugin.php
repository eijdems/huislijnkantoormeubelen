<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Plugin;

use Emizen\CustomAttachment\Model\ResourceModel\ProjectImage as ProjectImageResource;
use FME\Productattachments\Model\Productattachments\DataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Injects saved Project Images into the attachment edit form so the fileUploader
 * renders existing thumbnails (FME's DataProvider returns records keyed by attachment id).
 */
class DataProviderPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param RequestInterface $request
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        RequestInterface $request,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem
    ) {
        $this->request = $request;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * @param DataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(DataProvider $subject, $result)
    {
        if (!is_array($result)) {
            return $result;
        }

        $attachmentId = (int)$this->request->getParam('id');
        if (!$attachmentId || !isset($result[$attachmentId]) || !is_array($result[$attachmentId])) {
            return $result;
        }

        $images = $this->getImages($attachmentId);
        if ($images) {
            $result[$attachmentId]['project_images'] = $images;
        }

        return $result;
    }

    /**
     * Build the fileUploader preview payload for saved images.
     *
     * @param int $attachmentId
     * @return array
     */
    private function getImages(int $attachmentId): array
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(ProjectImageResource::TABLE_NAME);
        $select = $connection->select()
            ->from($table, ['image'])
            ->where('attachment_id = ?', $attachmentId)
            ->order('position ASC')
            ->order('entity_id ASC');

        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        $images = [];
        foreach ($connection->fetchCol($select) as $relativePath) {
            $item = [
                'name' => basename((string)$relativePath),
                'url' => $mediaUrl . ltrim((string)$relativePath, '/'),
            ];
            try {
                if ($mediaDirectory->isExist($relativePath)) {
                    $item['size'] = (int)$mediaDirectory->stat($relativePath)['size'];
                }
            } catch (\Exception $e) {
                // Missing file on disk: still show the entry so the admin can re-upload.
                $item['size'] = 0;
            }
            $images[] = $item;
        }

        return $images;
    }
}
