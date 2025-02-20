<?php
namespace Emizen\Download\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\RequestInterface;

class RestrictCmsPage implements ObserverInterface
{
    protected $customerSession;
    protected $redirect;
    protected $request;
    protected $actionFlag;

    public function __construct(
        Session $customerSession,
        RedirectInterface $redirect,
        RequestInterface $request,
        ActionFlag $actionFlag
    ) {
        $this->customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->actionFlag = $actionFlag;
    }

    public function execute(Observer $observer)
    {
        // Restrict access to CMS Page ID = 9
        $restrictedPageId = 9;
        $currentPageId = (int) $this->request->getParam('page_id');
        //echo $currentPageId;die;
        // Check if user is NOT logged in and trying to access restricted page
        if ($currentPageId === $restrictedPageId && !$this->customerSession->isLoggedIn()) {
            $observer->getControllerAction()->getResponse()->setRedirect('/customer/account/login');
            $this->actionFlag->set('', 'no-dispatch', true);
        }
    }
}
