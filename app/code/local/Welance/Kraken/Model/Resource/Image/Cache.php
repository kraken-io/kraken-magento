<?php

class Welance_Kraken_Model_Resource_Image_Cache extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('welance_kraken/image_cache', 'id');
    }
}