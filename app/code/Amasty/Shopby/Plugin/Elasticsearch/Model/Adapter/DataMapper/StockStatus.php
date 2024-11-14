<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapper;

use Amasty\Shopby\Model\ConfigProvider;
use Amasty\Shopby\Model\Inventory\Resolver;
use Amasty\Shopby\Model\Layer\Filter\Stock as FilterStock;
use Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\DataMapperInterface;
use Magento\Framework\App\ObjectManager;

class StockStatus implements DataMapperInterface
{
    public const FIELD_NAME = 'stock_status';

    public const DOCUMENT_FIELD_NAME = 'quantity_and_stock_status';

    public const INDEX_DOCUMENT = 'document';

    /**
     * @var array
     */
    private $stockProductIds = [];

    /**
     * @var Resolver
     */
    private $inventoryResolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ?\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, // @deprecated
        ?\Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatusResource, // @deprecated
        ?\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, // @deprecated
        ?\Magento\Store\Model\StoreManagerInterface $storeManager, // @deprecated
        Resolver $inventoryResolver = null, // TODO not optional
        ConfigProvider $configProvider = null // TODO not optional
    ) {
        $this->inventoryResolver = $inventoryResolver ?? ObjectManager::getInstance()->get(Resolver::class);
        $this->configProvider = $configProvider ?? ObjectManager::getInstance()->get(ConfigProvider::class);
    }

    /**
     * @param int $entityId
     * @param array $entityIndexData
     * @param int $storeId
     * @param array $context
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function map($entityId, array $entityIndexData, $storeId, $context = []): array
    {
        if (!isset($context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME])
            || $this->configProvider->isStockByReservedQty()
        ) {
            $value = $this->isProductInStock($entityId, (int)$storeId);
        } else {
            $value = $context[self::INDEX_DOCUMENT][self::DOCUMENT_FIELD_NAME];
        }

        return [self::FIELD_NAME => $value];
    }

    private function isProductInStock(int $entityId, int $storeId): int
    {
        if (!array_key_exists($storeId, $this->stockProductIds)) {
            $this->stockProductIds[$storeId] = $this->inventoryResolver->getProductStock($storeId);
        }

        if (!isset($this->stockProductIds[$storeId][$entityId])) {
            return FilterStock::FILTER_DEFAULT;
        }

        if ((int)$this->stockProductIds[$storeId][$entityId]) {
            return FilterStock::FILTER_IN_STOCK;
        }

        return FilterStock::FILTER_OUT_OF_STOCK;
    }

    public function isAllowed(?int $storeId = null): bool
    {
        return $this->configProvider->isStockFilterEnabled($storeId);
    }

    public function getFieldName(): string
    {
        return self::FIELD_NAME;
    }

    /**
     * @param int $storeId
     * @param int[] $productIds
     */
    public function preloadCacheData(int $storeId, array $productIds): void
    {
        $this->stockProductIds[$storeId] = $this->inventoryResolver->getProductStock($storeId, $productIds);
    }

    public function clearCacheData(): void
    {
        $this->stockProductIds = [];
    }
}
