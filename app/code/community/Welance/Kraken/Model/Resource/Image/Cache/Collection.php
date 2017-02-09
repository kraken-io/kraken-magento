<?php

class Welance_Kraken_Model_Resource_Image_Cache_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('welance_kraken/image_cache');
    }
}