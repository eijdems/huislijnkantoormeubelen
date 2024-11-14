<?php

namespace Emizen\Customsearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\Session;

class Data extends AbstractHelper
{
    protected $searchConfigProvider;
    protected $_session;

    public function __construct(
        \Magento\Search\ViewModel\ConfigProvider $searchConfigProvider,
        Session $session
    ) {
        $this->searchConfigProvider = $searchConfigProvider;
        $this->_session = $session;

    }

    public function getSearchconfigProvider()
    {
        return $this->searchConfigProvider;
    }
    public function getCustomLogin()
    {
        return $this->_session->isLoggedIn();
    }
}