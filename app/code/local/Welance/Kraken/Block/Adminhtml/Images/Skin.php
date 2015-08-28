<?php

class Welance_Kraken_Block_Adminhtml_Images_Skin extends Mage_Core_Block_Template
{
    public function getSkinImageCount()
    {
        return Mage::helper('welance_kraken')->getImageCount(Welance_Kraken_Model_Abstract::TYPE_SKIN);
    }

    public function getSkinImageFolderCount()
    {
        return Mage::helper('welance_kraken')->countImages(Welance_Kraken_Model_Abstract::TYPE_SKIN);
    }

    public function getNewImagesAsJson()
    {
        $helper = Mage::helper('welance_kraken');

        $images = $helper->getAllImages(Welance_Kraken_Model_Abstract::TYPE_SKIN);

        $i = 0;

        foreach($images as $image){
            if($helper->imageExits(Welance_Kraken_Model_Abstract::TYPE_SKIN,$image['dir'],$image['name'],$image['checksum'])){
                unset($images[$i]);
            }
            $i++;
        }

        return json_encode(array_values($images));
    }
}