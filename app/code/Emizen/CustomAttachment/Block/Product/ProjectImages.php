<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Emizen\CustomAttachment\Block\Product;

use Emizen\CustomAttachment\Model\ResourceModel\ProjectImage as ProjectImageResource;
use Emizen\Customsearch\Helper\Data as LoginHelper;
use FME\Productattachments\Helper\Data as AttachmentHelper;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\UrlInterface;

/**
 * Renders a full-width "Project Images" gallery on the product page, grouped by the
 * attachment's category. Each image links to FME's download controller for the
 * attachment's file (preserving the download counter, logging and limit/group gating).
 */
class ProjectImages extends View
{
    /**
     * @var AttachmentHelper
     */
    private $attachmentHelper;

    /**
     * @var LoginHelper
     */
    private $loginHelper;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array|null
     */
    private $groupedImages;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param AttachmentHelper $attachmentHelper
     * @param LoginHelper $loginHelper
     * @param ResourceConnection $resourceConnection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        AttachmentHelper $attachmentHelper,
        LoginHelper $loginHelper,
        ResourceConnection $resourceConnection,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->attachmentHelper = $attachmentHelper;
        $this->loginHelper = $loginHelper;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Whether the current visitor may see the section (logged-in customers only).
     *
     * @return bool
     */
    public function getCustomLogin(): bool
    {
        return (bool)$this->loginHelper->getCustomLogin();
    }

    /**
     * Project images for the current product, grouped by attachment category.
     *
     * @return array [ ['category' => string, 'images' => [['url','download_url','title'], ...]], ... ]
     */
    public function getGroupedProjectImages(): array
    {
        if ($this->groupedImages !== null) {
            return $this->groupedImages;
        }

        $this->groupedImages = [];
        $product = $this->getProduct();
        if (!$product || !$product->getId()) {
            return $this->groupedImages;
        }

        $attachments = $this->attachmentHelper->getProductAttachmentsById((int)$product->getId());
        if (!is_array($attachments) || !$attachments) {
            return $this->groupedImages;
        }

        // Keep only enabled attachments that actually have a downloadable file.
        $attachmentsById = [];
        foreach ($attachments as $attachment) {
            if ((int)($attachment['status'] ?? 0) !== 1) {
                continue;
            }
            if (empty($attachment['filename'])) {
                continue;
            }
            $attachmentsById[(int)$attachment['productattachments_id']] = $attachment;
        }
        if (!$attachmentsById) {
            return $this->groupedImages;
        }

        $imagesByAttachment = $this->fetchImages(array_keys($attachmentsById));
        if (!$imagesByAttachment) {
            return $this->groupedImages;
        }

        $categoryNames = $this->fetchCategoryNames($attachmentsById);
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        $groups = [];
        foreach ($attachmentsById as $attachmentId => $attachment) {
            if (empty($imagesByAttachment[$attachmentId])) {
                continue;
            }
            $catId = (int)($attachment['cat_id'] ?? 0);
            if (!isset($groups[$catId])) {
                $groups[$catId] = [
                    'category' => $categoryNames[$catId] ?? (string)($attachment['title'] ?? ''),
                    'images' => [],
                ];
            }
            $downloadUrl = $this->getDownloadUrl($attachmentId);
            foreach ($imagesByAttachment[$attachmentId] as $relativePath) {
                $groups[$catId]['images'][] = [
                    'url' => $mediaUrl . ltrim($relativePath, '/'),
                    'download_url' => $downloadUrl,
                    'title' => (string)($attachment['title'] ?? ''),
                ];
            }
        }

        $this->groupedImages = array_values($groups);
        return $this->groupedImages;
    }

    /**
     * FME download URL for an attachment (counter/log/limit/group gating preserved).
     *
     * @param int $attachmentId
     * @return string
     */
    public function getDownloadUrl(int $attachmentId): string
    {
        return $this->getUrl('productattachments/index/download', ['id' => $attachmentId]);
    }

    /**
     * @param int[] $attachmentIds
     * @return array<int, string[]>
     */
    private function fetchImages(array $attachmentIds): array
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName(ProjectImageResource::TABLE_NAME);
        $select = $connection->select()
            ->from($table, ['attachment_id', 'image'])
            ->where('attachment_id IN (?)', $attachmentIds)
            ->order('attachment_id ASC')
            ->order('position ASC')
            ->order('entity_id ASC');

        $result = [];
        foreach ($connection->fetchAll($select) as $row) {
            $result[(int)$row['attachment_id']][] = (string)$row['image'];
        }

        return $result;
    }

    /**
     * Resolve attachment category (productattachments_cats) names.
     *
     * @param array $attachmentsById
     * @return array<int, string>
     */
    private function fetchCategoryNames(array $attachmentsById): array
    {
        $catIds = [];
        foreach ($attachmentsById as $attachment) {
            $catId = (int)($attachment['cat_id'] ?? 0);
            if ($catId) {
                $catIds[$catId] = $catId;
            }
        }
        if (!$catIds) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('productattachments_cats');
        $select = $connection->select()
            ->from($table, ['category_id', 'category_name'])
            ->where('category_id IN (?)', array_values($catIds));

        $names = [];
        foreach ($connection->fetchAll($select) as $row) {
            $names[(int)$row['category_id']] = (string)$row['category_name'];
        }

        return $names;
    }
}
