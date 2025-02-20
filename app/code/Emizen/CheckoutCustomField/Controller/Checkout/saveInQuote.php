<?php
namespace Emizen\CheckoutCustomField\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteRepository;

class saveInQuote extends Action
{
    protected $resultForwardFactory;
    protected $layoutFactory;
    protected $cart;
    protected $checkoutSession;
    protected $quoteRepository;
    

    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        LayoutFactory $layoutFactory,
        Cart $cart,
        Session $checkoutSession,
        QuoteRepository $quoteRepository
    )
    {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;

        parent::__construct($context);
    }

    public function execute()
    {   
        $checkVal = (int)$this->getRequest()->getParam('checkVal');
        $referenceNumber = $this->getRequest()->getParam('reference_number'); // Retrieve the reference number
        $uploadedFileNames = []; // Array to store filenames of uploaded files

        if (isset($_FILES['uploadedFiles']) && is_array($_FILES['uploadedFiles']['name'])) {
            $files = $_FILES['uploadedFiles'];

            // Define a directory for saving the uploaded files
            $targetDir = BP . '/pub/media/upload/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            foreach ($files['name'] as $key => $name) {
                if ($files['error'][$key] === UPLOAD_ERR_OK) {
                    // Create a unique filename to prevent conflicts
                    $fileName = uniqid() . '_' . basename($name);
                    $targetFilePath = $targetDir . $fileName;

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($files['tmp_name'][$key], $targetFilePath)) {
                        $uploadedFileNames[] = $fileName; // Store each filename
                    } else {
                        $this->messageManager->addErrorMessage(__('Failed to upload file: %1', $name));
                    }
                }
            }
        }

        $quoteId = $this->checkoutSession->getQuoteId();
        $quote = $this->quoteRepository->get($quoteId);
        $quote->setAgree($checkVal);
        $quote->setReferenceNumber($referenceNumber); 

        if (!empty($uploadedFileNames)) {
            // Store only the filenames as a JSON array or custom format
            $quote->setCustomFile(json_encode($uploadedFileNames)); // Assuming `custom_file` is a field in the quote
        }

        $quote->save();
    }

}