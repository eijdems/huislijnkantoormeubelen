<?php

namespace Emizen\CustomDownload\Controller\Index;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index extends Action
{
    protected $resultPageFactory;
    protected $customerSession;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession // Inject the customer session model
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        // Check if the user is logged in
        if (!$this->customerSession->isLoggedIn()) {
            // If not logged in, store the current page URL in the session to redirect after login
            $redirectUrl = $this->_url->getCurrentUrl();
            $this->customerSession->setBeforeAuthUrl($redirectUrl);

            // Redirect to login page
            return $this->resultRedirectFactory->create()->setUrl($this->_url->getUrl('customer/account/login'));
        }

        // If logged in, proceed with rendering the page
        return $this->resultPageFactory->create();
    }
}
