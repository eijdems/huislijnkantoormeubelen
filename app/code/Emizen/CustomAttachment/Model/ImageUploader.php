<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Generic, image-only uploader for attachment Project Images.
 *
 * baseTmpPath == basePath (mirrors FME's convention) so uploaded files land
 * directly in their permanent media directory and no tmp move is required.
 */
class ImageUploader
{
    /**
     * @var Database
     */
    private $coreFileStorageDatabase;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string[]
     */
    private $allowedExtensions;

    /**
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param string $basePath
     * @param string[] $allowedExtensions
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        string $basePath = 'emizen/project_images',
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->basePath = trim($basePath, '/');
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Base media-relative directory where images are stored.
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Upload a single file (sent under $fileId) into the media directory.
     *
     * @param string $fileId POST field name the UI fileUploader posts under
     * @return array
     * @throws LocalizedException
     */
    public function saveFileToTmpDir(string $fileId): array
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($this->basePath));
        if (!$result) {
            throw new LocalizedException(__('File cannot be saved to the destination folder.'));
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->basePath . '/' . $result['file'];
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = $this->basePath . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(__('Something went wrong while saving the file(s).'));
            }
        }

        return $result;
    }
}
