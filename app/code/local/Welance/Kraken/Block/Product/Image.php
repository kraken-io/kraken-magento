<?php

class Welance_Kraken_Block_Product_Image extends Mage_Catalog_Block_Product_View
{
    public function getConcurrency(){
        return Mage::getStoreConfig('welance_kraken/kraken_config/frontend_concurrency');
    }

    public function getImagesToOptimize()
    {
        $product = $this->getProduct();

        $productImages = $this->_getProductImages($product);

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table = $resource->getTableName('welance_kraken/image_cache');

        $query = "SELECT * FROM {$table} WHERE (`is_running` = 0 AND `is_processed` = 0) AND (`file_name` LIKE ";
        $i = 0;
        $productImagesCount = count($productImages);

        foreach ($productImages as $productImage) {
            $query .= "'%".$productImage."%'";

            $i++;

            if ($i < $productImagesCount) {
                $query .= " OR `file_name` LIKE ";
            } else {
                $query .= ")";
            }
        }

        $cacheImages = array();

        if ($productImagesCount > 0) {
            foreach($readConnection->fetchAll($query) as $cacheImage){
                $cacheImages[] = array(
                    'id' =>$cacheImage['id'],
                    'product_id' => $product->getId()
                );
            }
        }

        return json_encode($cacheImages);
    }

    protected function _getProductImages($product)
    {
        $images = $product->getMediaGalleryImages();

        $productImages = array();

        foreach($images as $image){
            $productImages[] = $image['file'];
        }

        return $productImages;
    }
}