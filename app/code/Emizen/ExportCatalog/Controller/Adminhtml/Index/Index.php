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
use PhpOffice\PhpSpreadsheet\Style\Font;

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
            // Fetch all categories
            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection->addAttributeToSelect('*'); // Fetch all attributes

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;

            foreach ($categoryCollection as $category) {
                $categoryId = $category->getId();
                $categoryTitle = $category->getName();

                // Skip the "Default Category"
                if ($categoryId == 1 || $categoryId == 8) {
                    continue;
                }
                // Fetch products for current category
                $productCollection = $this->productCollectionFactory->create();
                $productCollection->addCategoriesFilter(['eq' => $categoryId]);
                $productCollection->addAttributeToSelect(['name', 'sku', 'price']); // Ensure product name and SKU are loaded
                $countProduct = count($productCollection);
                $categoryPage = "Category";

                
                // Write category title in bold
                $sheet->setCellValue('A' . $row, $categoryPage);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, strtoupper($categoryTitle));
                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('C' . $row, $countProduct);
                $sheet->getStyle('C' . $row)->getFont()->setBold(true);
                $row++;

                

                foreach ($productCollection as $product) {
                    //var_dump($product->debug());die;
                    $productPage = $categoryTitle;
                    $sheet->setCellValue('A' . $row, $productPage);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $sheet->setCellValue('B' . $row, $product->getSku());
                    $sheet->setCellValue('C' . $row, $product->getName());
                    $sheet->setCellValue('D' . $row, $product->getPrice());                    
                    $row++;
                }

                // Add an empty row after each category for better readability
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
            unlink($tempFilePath);

            return $this->fileFactory->create(
                $fileName,
                $fileContent,
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }
    }
}
