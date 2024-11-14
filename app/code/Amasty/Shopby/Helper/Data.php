<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Helper;

use Amasty\Shopby\Model\Config\MobileConfigResolver;
use Amasty\Shopby\Model\ConfigProvider;
use Amasty\Shopby\Model\Layer\Filter\Decimal as DecimalFilter;
use Amasty\Shopby\Model\Layer\Filter\Price as PriceFilter;
use Amasty\Shopby\Model\Layer\GetSelectedFiltersSettings;
use Amasty\Shopby\Model\Layer\IsBrandPage;
use Amasty\Shopby\Model\Request;
use Amasty\ShopbyBase\Api\UrlBuilderInterface;
use Amasty\ShopbyBase\Model\Detection\MobileDetect;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Amasty\Shopby;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Amasty\ShopbyBase\Helper\OptionSetting as OptionSettingHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Data as SwatchesHelper;

class Data extends AbstractHelper
{
    public const UNFOLDED_OPTIONS_STATE = 'amshopby/general/unfolded_options_state';

    /**
     * @deprecated setting moved to ConfigProvider
     * @see \Amasty\ShopbyBase\Model\ConfigProvider
     */
    public const AMSHOPBY_ROOT_GENERAL_URL_PATH = 'amshopby_root/general/url';

    /**
     * @deprecated setting moved to ConfigProvider
     * @see \Amasty\ShopbyBase\Model\ConfigProvider
     */
    public const AMSHOPBY_ROOT_ENABLED_PATH = 'amshopby_root/general/enabled';
    public const CATALOG_SEO_SUFFIX_PATH = 'catalog/seo/category_url_suffix';
    public const AMSHOPBY_INDEX_INDEX = 'amshopby_index_index';
    public const SHOPBY_AJAX = 'shopbyAjax';

    /**
     * @var  Layer
     */
    protected $layer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Request
     */
    protected $shopbyRequest;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var SwatchesHelper
     */
    private $swatchHelper;
    /**
     * @var OptionSettingHelper
     */
    private $optionSettingHelper;

    /**
     * @var UrlBuilderInterface
     */
    private $amUrlBuilder;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    public function __construct(
        Context $context,
        Resolver $layerResolver,
        StoreManagerInterface $storeManager,
        Request $shopbyRequest,
        SwatchesHelper $swatchHelper,
        OptionSettingHelper $optionSettingHelper,
        Registry $registry,
        UrlBuilderInterface $amUrlBuilder,
        MobileDetect $mobileDetect
    ) {
        parent::__construct($context);
        $this->layerResolver = $layerResolver;
        $this->storeManager = $storeManager;
        $this->shopbyRequest = $shopbyRequest;
        $this->registry = $registry;
        $this->swatchHelper = $swatchHelper;
        $this->optionSettingHelper = $optionSettingHelper;
        $this->amUrlBuilder = $amUrlBuilder;
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * @return array
     * @deprecated
     */
    public function getSelectedFiltersSettings()
    {
        return ObjectManager::getInstance()->get(GetSelectedFiltersSettings::class)->execute();
    }

    /**
     * @deprecated moved to separate class
     * @see MobileConfigResolver::isAjaxEnabled()
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return ObjectManager::getInstance()
            ->get(MobileConfigResolver::class)
            ->isAjaxEnabled();
    }

    /**
     * @deprecated moved to separate class
     * @see ConfigProvider::isAjaxEnabled()
     * @return bool
     */
    public function isAjaxSettingEnabled()
    {
        return ObjectManager::getInstance()
            ->get(ConfigProvider::class)
            ->isAjaxEnabled();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTooltipUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $tooltipImage = $this->scopeConfig->getValue('amshopby/tooltips/image', ScopeInterface::SCOPE_STORE);
        if (empty($tooltipImage)) {
            return '';
        }
        return $baseUrl . $tooltipImage;
    }

    /**
     * @param Shopby\Model\Layer\Filter\Item $filterItem
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isFilterItemSelected(\Amasty\Shopby\Model\Layer\Filter\Item $filterItem)
    {
        $filter = $filterItem->getFilter();
        $data = $this->shopbyRequest->getFilterParam($filter);

        if (!empty($data)) {
            $ids = explode(',', $data);
            if ($this->isNeedCheckOption($filter)) {
                $ids = array_map('intval', $ids ?? []);
            }

            if (in_array($filterItem->getValue(), $ids)) {
                return 1;
            }
        }

        return 0;
    }

    private function isNeedCheckOption(AbstractFilter $filter): bool
    {
        if ($filter instanceof DecimalFilter
            || $filter instanceof PriceFilter
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item[] $activeFilters
     * @return string
     */
    public function getAjaxCleanUrl($activeFilters)
    {
        $filterState = [];

        foreach ($activeFilters as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }

        $filterState['p'] = null;
        $filterState['dt'] = null;
        $filterState['df'] = null;

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;

        return str_replace('&amp;', '&', $this->amUrlBuilder->getUrl('*/*/*', $params));
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->getLayer()->getCurrentCategory();
    }

    /**
     * @return string|null
     */
    public function getThumbnailPlaceholder()
    {
        return $this->scopeConfig->getValue('catalog/category_placeholder/thumbnail');
    }

    /**
     * @deprecated moved to separate class
     * @see \Amasty\ShopbyBase\Model\ConfigProvider::getSubmitFiltersMobile
     */
    public function getSubmitFiltersDesktop(): int
    {
        return ObjectManager::getInstance()
            ->get(\Amasty\ShopbyBase\Model\ConfigProvider::class)
            ->getSubmitFiltersDesktop();
    }

    /**
     * @deprecated moved to separate class
     * @see \Amasty\ShopbyBase\Model\ConfigProvider::getSubmitFiltersMobile
     */
    public function getSubmitFiltersMobile(): int
    {
        return ObjectManager::getInstance()
            ->get(\Amasty\ShopbyBase\Model\ConfigProvider::class)
            ->getSubmitFiltersMobile();
    }

    /**
     * @deprecated
     * @see MobileConfigResolver::getSubmitFilterMode
     */
    public function collectFilters(): int
    {
        return ObjectManager::getInstance()
            ->get(MobileConfigResolver::class)
            ->getSubmitFilterMode();
    }

    /**
     * @return int
     */
    public function getUnfoldedCount()
    {
        return (int)$this->scopeConfig->getValue(self::UNFOLDED_OPTIONS_STATE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param array $optionIds
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return array
     */
    public function getSwatchesFromImages($optionIds, \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $swatches = [];
        if (!$this->swatchHelper->isVisualSwatch($attribute) && !$this->swatchHelper->isTextSwatch($attribute)) {
            /**
             * @TODO use collection method
             */
            foreach ($optionIds as $optionId) {
                $setting = $this->optionSettingHelper->getSettingByValue(
                    $optionId,
                    $attribute->getAttributeCode(),
                    $this->storeManager->getStore()->getId()
                );

                $swatches[$optionId] = [
                    'type' => 'option_image',
                    'value' => $setting->getSliderImageUrl()
                ];
            }
        }

        return $swatches;
    }

    /**
     * @deprecated moved to separate class
     * @see \Amasty\ShopbyBase\Model\ConfigProvider
     * @return string
     */
    public function getAllProductsUrlKey()
    {
        return ObjectManager::getInstance()
            ->get(\Amasty\ShopbyBase\Model\ConfigProvider::class)
            ->getAllProductsUrlKey();
    }

    /**
     * @deprecated moved to separate class
     * @see \Amasty\ShopbyBase\Model\AllProductsConfig
     * @return bool
     */
    public function isAllProductsEnabled()
    {
        return ObjectManager::getInstance()
            ->get(\Amasty\ShopbyBase\Model\AllProductsConfig::class)
            ->isAllProductsAvailable();
    }

    /**
     * @return mixed
     */
    public function getCatalogSeoSuffix()
    {
        return (string)$this->scopeConfig->getValue(
            self::CATALOG_SEO_SUFFIX_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return Layer
     */
    public function getLayer()
    {
        if (!$this->layer) {
            $this->layer = $this->layerResolver->get();
        }
        return $this->layer;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return parent::_getRequest();
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isBrandPage(): bool
    {
        return ObjectManager::getInstance()->get(IsBrandPage::class)->execute();
    }

    /**
     * @return bool
     */
    public function isShopbyPageWithAjax()
    {
        return $this->getRequest()->getParam(self::SHOPBY_AJAX)
            && $this->getRequest()->getFullActionName() == self::AMSHOPBY_INDEX_INDEX;
    }
}
