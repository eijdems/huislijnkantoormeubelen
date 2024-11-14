<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Plugin\Ajax;

use Amasty\Shopby\Helper\Data;
use Amasty\Shopby\Helper\State;
use Amasty\Shopby\Model\Ajax\AjaxResponseBuilder;
use Amasty\Shopby\Model\Ajax\Counter\CounterDataProvider;
use Amasty\Shopby\Model\Ajax\RequestResponseUtils;
use Amasty\Shopby\Model\Layer\Cms\Manager;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Url\Decoder;
use Magento\Framework\Url\Encoder;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page;

/**
 * @deprecated class decomposed for better extensibility
 * @see AjaxResponseBuilder
 */
class Ajax
{
    public const OSN_CONFIG = 'amasty.xnotif.config';

    public const QUICKVIEW_CONFIG = 'amasty.quickview.config';

    public const SORTING_CONFIG = 'amasty.sorting.direction';

    public const ILN_FILTER_ANALYTICS = 'amasty.shopby.filter_analytics';

    public const CUSTOM_THEME_LAYOUT_MAPPING = [
        'fcnet/blank_julbo' => [
            'image' => 'category.image',
            'description' => 'category_desc_main_column'
        ],
        'Smartwave/Porto' => [
            'image' => 'category.image',
            'description' => 'category_desc_main_column'
        ],
        'Amasty/JetTheme' => [
            'image' => 'category.image',
            'description' => 'category.description'
        ]
    ];

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Encoder
     */
    protected $urlEncoder;

    /**
     * @var Decoder
     */
    protected $urlDecoder;

    /**
     * @var State
     */
    protected $stateHelper;

    /**
     * @var DesignInterface
     */
    protected $design;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var Manager
     */
    private $cmsManager;

    /**
     * @var CounterDataProvider
     */
    protected $counterDataProvider;

    /**
     * @var AjaxResponseBuilder
     */
    private $ajaxResponseBuilder;

    /**
     * @var RequestResponseUtils
     */
    private $utils;

    public function __construct(
        Data $helper,
        RawFactory $resultRawFactory,
        Encoder $urlEncoder,
        Decoder $urlDecoder,
        State $stateHelper,
        DesignInterface $design,
        ActionFlag $actionFlag,
        Manager $cmsManager,
        ?LayoutInterface $layout,
        ?Config $pageConfig,
        ?DataObjectFactory $dataObjectFactory,
        ?ManagerInterface $eventManager,
        CounterDataProvider $counterDataProvider,
        AjaxResponseBuilder $ajaxResponseBuilder = null,
        RequestResponseUtils $utils = null
    ) {
        $this->helper = $helper;
        $this->resultRawFactory = $resultRawFactory;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
        $this->stateHelper = $stateHelper;
        $this->design = $design;
        $this->actionFlag = $actionFlag;
        $this->cmsManager = $cmsManager;
        $this->counterDataProvider = $counterDataProvider;
        // OM for backward compatibility
        $this->ajaxResponseBuilder = $ajaxResponseBuilder ??
            ObjectManager::getInstance()->get(AjaxResponseBuilder::class);
        $this->utils = $utils ?? ObjectManager::getInstance()->get(RequestResponseUtils::class);
    }

    /**
     * @deprecated
     * @see RequestResponseUtils::isAjaxNavigation
     */
    protected function isAjax(RequestInterface $request)
    {
        return $this->utils->isAjaxNavigation($request);
    }

    /**
     * @deprecated
     * @see RequestResponseUtils::isCounterRequest
     */
    protected function isCounterRequest(RequestInterface $request): bool
    {
        return $this->utils->isCounterRequest($request);
    }

    /**
     * @return array
     * @deprecated
     * @see AjaxResponseBuilder::build
     */
    protected function getAjaxResponseData($page = null)
    {
        return $this->ajaxResponseBuilder->build();
    }

    /**
     * @deprecated
     * @see RequestResponseUtils::prepareResponse
     */
    protected function prepareResponse(array $data)
    {
        return $this->utils->prepareResponse($data);
    }

    /**
     * @return Manager
     * @see __construct
     * @deprecated use DI
     */
    public function getCmsManager()
    {
        return $this->cmsManager;
    }

    /**
     * @return ActionFlag
     * @see __construct
     * @deprecated use DI
     */
    public function getActionFlag()
    {
        return $this->actionFlag;
    }
}
