<?php

class Welance_Kraken_Block_Adminhtml_Statistics extends Mage_Adminhtml_Block_Widget_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('kraken/statistics.phtml');
    }

    public function getOptimizedImagesCount($type)
    {
        $helper = Mage::helper('welance_kraken');

        return $helper->getImageCount($type);
    }

    public function getSavedImageSize($type)
    {
        return $this->_getSizeSum('saved_file_size', $type);
    }

    public function getTransferedImageSize($type)
    {
        return $this->_getSizeSum('original_size', $type);
    }

    public function getQuota()
    {
        return Mage::helper('welance_kraken/api')->getUserStatus();
    }

    protected function _getSizeSum($column,$type)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        if ($type == Welance_Kraken_Model_Abstract::TYPE_CACHE) {
            $table = $resource->getTableName('welance_kraken/image_'  .$type);
        } else {
            $table = $resource->getTableName('welance_kraken/images_' . $type);
        }

        $query = "SELECT SUM(" . $column . ") AS " . $column . " FROM {$table}";
        $size = $readConnection->fetchOne($query);

        return $size;
    }
}