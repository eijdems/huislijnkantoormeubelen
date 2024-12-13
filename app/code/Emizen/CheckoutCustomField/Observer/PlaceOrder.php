<?php
namespace Emizen\CheckoutCustomField\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;
use Psr\Log\LoggerInterface;

class PlaceOrder implements ObserverInterface
{
    /**
    * @var \Psr\Log\LoggerInterface
    */
    protected $_logger;

    /**
    * @var \Magento\Customer\Model\Session
    */
    protected $quoteFactory;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */

    public function __construct(LoggerInterface $logger,
        QuoteFactory $quoteFactory) {
        $this->_logger = $logger;
        $this->quoteFactory = $quoteFactory;
    }

    public function execute(Observer $observer)
    {
        // Retrieve the order object
        $order = $observer->getOrder();
        $quoteId = $order->getQuoteId();
        
        // Load the corresponding quote
        $quote = $this->quoteFactory->create()->load($quoteId);
        
        // Set the custom fields on the order
        $order->setAgree($quote->getAgree());  // Set the 'agree' field
        $order->setReferenceNumber($quote->getReferenceNumber());  // Set the 'reference_number' field
        
        // Retrieve and save the uploaded file (custom_file)
        $uploadedFile = $quote->getCustomFile();  // Get the uploaded file path or URL from the quote

        if ($uploadedFile) {
            // Set the uploaded file path on the order
            $order->setCustomFile($uploadedFile);  // Set the 'custom_file' field
        }

        // Save the updated order
        $order->save();
    }
}