<?php
namespace Emizen\CustomDownload\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Helper\Image;

class Attachments extends Template
{
    protected $resourceConnection;
    protected $categoryCollectionFactory;
    protected $productRepository;
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        ResourceConnection $resourceConnection,
        CollectionFactory $categoryCollectionFactory,
        ProductRepository $productRepository,
        Image $imageHelper,
        array $data = []
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function getCategoryAttachments()
    {
        // Get the database connection
        $connection = $this->resourceConnection->getConnection();

        // SQL Query to fetch categories and their attachments
        $sql = "
            SELECT 
                pac.category_id, 
                pac.category_name, 
                pac.category_image, 
                pac.category_url_key, 
                pac.status AS category_status, 
                pac.parent_category_id, 
                pac.category_order, 
                pac.path, 
                pac.level, 
                pac.children_counts, 
                pac.is_visible_front, 
                pac.is_visible_prod, 
                pac.meta_title, 
                pac.meta_desc, 
                pac.meta_keywords, 
                pac.created_at AS category_created_at, 
                pac.updated_at AS category_updated_at,
                pa.productattachments_id,
                pa.title AS attachment_title,
                pa.filename,
                pa.file_icon,
                pa.file_type,
                pa.file_size,
                pa.download_link,
                pa.block_position,
                pa.link_url,
                pa.link_title,
                pa.embed_video,
                pa.video_title,
                pa.downloads,
                pa.content,
                pa.status AS attachment_status,
                pa.cmspage_id,
                pa.customer_group_id,
                pa.limit_downloads,
                pa.created_time AS attachment_created_time,
                pa.update_time AS attachment_update_time,
                pa.product_names
            FROM 
                productattachments_cats AS pac
            LEFT JOIN 
                productattachments AS pa 
                ON pac.category_id = pa.cat_id
            WHERE 
                pac.parent_category_id = 3  -- Fetch child categories of category with ID 3
                AND pac.status = 1  -- Active categories only
            ORDER BY 
                pac.category_order, pac.category_name
        ";

        // Fetch the results
        return $connection->fetchAll($sql);
    }

    // Get image URL for product
    public function getProductImageUrl($product)
    {
        return $this->imageHelper->init($product, 'product_base_image')->getUrl();
    }

    // Get related products
    public function getRelatedProducts($product)
    {
        $relatedProducts = $product->getRelatedProducts();
        // Ensure related products is always an array
        return is_array($relatedProducts) ? $relatedProducts : [];
    }
    public function getProductRepository()
    {
        return $this->productRepository;
    }
}
