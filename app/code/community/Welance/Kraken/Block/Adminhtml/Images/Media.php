<?php

class Welance_Kraken_Block_Adminhtml_Images_Media extends Mage_Core_Block_Template
{
    public function getMediaImageCount()
    {
        return Mage::helper('welance_kraken')->getImageCount(Welance_Kraken_Model_Abstract::TYPE_MEDIA);
    }

    public function getMediaImageFolderCount()
    {
        return Mage::helper('welance_kraken')->countImages(Welance_Kraken_Model_Abstract::TYPE_MEDIA);
    }

    public function getNewImagesAsJson()
    {
        $helper = Mage::helper('welance_kraken');

        $images = $helper->getAllImages(Welance_Kraken_Model_Abstract::TYPE_MEDIA);

        $i = 0;
        foreach($images as $image){
            if($helper->imageExists(Welance_Kraken_Model_Abstract::TYPE_MEDIA,$image['dir'],$image['name'],$image['checksum'])){
                unset($images[$i]);
            }
            $i++;
        }


        return json_encode(array_values($images));
    }
}