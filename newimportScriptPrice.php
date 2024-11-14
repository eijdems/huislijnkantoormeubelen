<?php 
require __DIR__ . '/app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/import-new-script.log');
$logger = new \Zend_Log();
$logger->addWriter($writer);
$logger->info('Starting import process'); // Log start of the process

$productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

// Load all products
$products = $productCollectionFactory->create()
    ->addAttributeToSelect('*') // Select specific attributes to improve performance
    ->load();

foreach ($products as $product) {
    $productId = $product->getId(); // Fetch the product ID
    $productImage = $product->getImage();

    // Determine if the product has an image
    if ($productImage && $productImage != 'no_selection') {
        $haveImageValue = 0; // YES
        echo 'Product ID: ' . $productId . ' - Image: YES' . "\n";
    } else {
        $haveImageValue = 1; // NO
        echo 'Product ID: ' . $productId . ' - Image: NO' . "\n";
    }

    $product->setStoreId(0); // Ensure the change is for the default store view
    $product->setData('have_image_filter', $haveImageValue);
    $product->save();
}

$logger->info('Import process completed'); // Log end of the process

?>
