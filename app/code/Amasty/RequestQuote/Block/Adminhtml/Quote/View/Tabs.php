<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */

namespace Amasty\RequestQuote\Block\Adminhtml\Quote\View;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    private $quoteSession;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Amasty\RequestQuote\Model\Quote\Backend\Session $quoteSession,
        array $data = []
    ) {
        $this->quoteSession = $quoteSession;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @return \Amasty\RequestQuote\Api\Data\QuoteInterface
     */
    public function getQuote()
    {
       return $this->quoteSession->getQuote();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_quote_view_tabs');
        $this->setDestElementId('amasty_quote_view');
        $this->setTitle(__('Quote View'));
    }
}
