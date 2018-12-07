<?php

class Welance_Kraken_Block_Adminhtml_Images_Media extends Mage_Core_Block_Template
{
    protected $_mediaImages;

    protected $_mediaImageCount;

    protected $_mediaImageFolderCount;

    protected $_type = Welance_Kraken_Model_Abstract::TYPE_MEDIA;

    protected function _construct()
    {
        $cache = Mage::app()->getCache();

        if ($data = $cache->load('welance_kraken_' . $this->_type . '_images')) {
            $cacheData = unserialize($data);

            $this->_mediaImages = $cacheData['media_images'];
            $this->_mediaImageCount = $cacheData['media_image_count'];
            $this->_mediaImageFolderCount = $cacheData['media_image_folder_count'];
        }

        if (empty($this->_mediaImages)) {
            $helper = Mage::helper('welance_kraken');
            $_images = $helper->getAllImages($this->_type);
            usort($_images,array("Welance_Kraken_Helper_Data","cmp"));

            $this->_mediaImages = $_images;
            $this->_mediaImageCount = $helper->getImageCount($this->_type);
            $this->_mediaImageFolderCount = count($this->_mediaImages);

            $cacheData = array(
                'media_images' => $this->_mediaImages,
                'media_image_count' => $this->_mediaImageCount,
                'media_image_folder_count' => $this->_mediaImageFolderCount
            );

            $cache->save(serialize($cacheData),'welance_kraken_' . $this->_type . '_images');
        }

        parent::_construct();
    }

    public function getMediaImageCount()
    {
        return $this->_mediaImageCount;
    }

    public function getMediaImageFolderCount()
    {
        return $this->_mediaImageFolderCount;
    }

    public function getNewImagesAsJson()
    {
        if($this->_mediaImageCount == 0) {

            $images = $this->_mediaImages;

        } else if ($this->_mediaImageCount == $this->_mediaImageFolderCount) {

            $images = array();

        } else {

            $helper = Mage::helper('welance_kraken');
            $images = $helper->getNotOptimizedImages($this->_mediaImages, $this->_type);

        }

        return json_encode(array_values($images));
    }

}