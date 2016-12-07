<?php

class Welance_Kraken_Model_Product_Image extends Mage_Catalog_Model_Product_Image
{

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function saveFile()
    {
        parent::saveFile();

        /* added for Kraken Image Optimization */
        Mage::dispatchEvent('catalog_product_image_save_after', array($this->_eventObject => $this));

        return $this;
    }

}
