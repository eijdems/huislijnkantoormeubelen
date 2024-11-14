<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Emizen\ExportCatalog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Psr\Log\LoggerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Index extends Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * Export catalog data to Excel.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $productCollection = $this->productCollectionFactory->create()->addAttributeToSelect('*');
            $categoryCollection = $this->categoryCollectionFactory->create()->addAttributeToSelect('*');

            $products = [];
            foreach ($productCollection as $product) {
                $categories = $product->getCategoryIds();
                $categoryNames = [];
                foreach ($categories as $categoryId) {
                    $category = $this->categoryCollectionFactory->create()->addFieldToFilter('entity_id', $categoryId)->getFirstItem();
                    if ($category->getId()) {
                        $categoryNames[] = $category->getName();
                    }
                }
                $products[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'price' => $product->getPrice(),
                    'categories' => implode(', ', $categoryNames),
                ];
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Name');
            $sheet->setCellValue('C1', 'SKU');
            $sheet->setCellValue('D1', 'Price');
            $sheet->setCellValue('E1', 'Categories');

            // Add data
            $row = 2;
            foreach ($products as $product) {
                $sheet->setCellValue('A' . $row, $product['id']);
                $sheet->setCellValue('B' . $row, $product['name']);
                $sheet->setCellValue('C' . $row, $product['sku']);
                $sheet->setCellValue('D' . $row, $product['price']);
                $sheet->setCellValue('E' . $row, $product['categories']);
                $row++;
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'export_catalog.xlsx';

            $tempFilePath = tempnam(sys_get_temp_dir(), 'export');
            if ($tempFilePath === false) {
                throw new \RuntimeException('Failed to create temporary file.');
            }

            $writer->save($tempFilePath);

            $fileContent = file_get_contents($tempFilePath);
            unlink($tempFilePath); // Delete temporary file

            $response = $this->fileFactory->create(
                $fileName,
                $fileContent,
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );

            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Error exporting catalog: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('An error occurred while exporting the catalog.'));

            return $this->_redirect('*/*/'); // Redirect to the referring page or specific location
        }
    }
}
