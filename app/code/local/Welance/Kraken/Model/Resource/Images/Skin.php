<?php

class Welance_Kraken_Model_Resource_Images_Skin extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('welance_kraken/images_skin','id');
    }
}