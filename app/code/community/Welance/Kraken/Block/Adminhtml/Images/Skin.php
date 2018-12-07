<?php

class Welance_Kraken_Block_Adminhtml_Images_Skin extends Mage_Core_Block_Template
{
    protected $_skinImages;

    protected $_skinImageCount;

    protected $_skinImageFolderCount;

    protected $_type = Welance_Kraken_Model_Abstract::TYPE_SKIN;

    protected function _construct()
    {
        $cache = Mage::app()->getCache();

        if ($data = $cache->load('welance_kraken_' . $this->_type . '_images')) {
            $cacheData = unserialize($data);

            $this->_skinImages = $cacheData['skin_images'];
            $this->_skinImageCount = $cacheData['skin_image_count'];
            $this->_skinImageFolderCount = $cacheData['skin_image_folder_count'];
        }

        if (empty($this->_skinImages)) {
            $helper = Mage::helper('welance_kraken');
            $_images = $helper->getAllImages($this->_type);
            usort($_images,array("Welance_Kraken_Helper_Data","cmp"));

            $this->_skinImages = $_images;
            $this->_skinImageCount = $helper->getImageCount($this->_type);
            $this->_skinImageFolderCount = count($this->_skinImages);

            $cacheData = array(
                'skin_images' => $this->_skinImages,
                'skin_image_count' => $this->_skinImageCount,
                'skin_image_folder_count' => $this->_skinImageFolderCount
            );

            $cache->save(serialize($cacheData),'welance_kraken_' . $this->_type . '_images');
        }
    }


    public function getSkinImageCount()
    {
        return $this->_skinImageCount;
    }

    public function getSkinImageFolderCount()
    {
        return $this->_skinImageFolderCount;
    }

    public function getNewImagesAsJson()
    {
        if($this->_skinImages == 0) {

            $images = $this->_skinImages;

        } else if ($this->_skinImageCount == $this->_skinImageFolderCount) {

            $images = array();

        } else {

            $helper = Mage::helper('welance_kraken');
            $images = $helper->getNotOptimizedImages($this->_skinImages, $this->_type);

        }

        return json_encode(array_values($images));
    }
}