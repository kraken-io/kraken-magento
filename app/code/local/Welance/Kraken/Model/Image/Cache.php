<?php

class Welance_Kraken_Model_Image_Cache extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('welance_kraken/image_cache');
    }


    /**
     * @param $response
     * @return bool
     */

    public function saveResponse($response)
    {
        if ($response->success == true) {
            if ($response->original_size <= $response->kraked_size == false) {
                $this->setOriginalSize($response->original_size)
                    ->setSizeAfterUpload($response->kraked_size)
                    ->setSavedFileSize($response->saved_bytes)
                    ->save();

                return $this;
            }
        }

        return false;
    }


    /**
     * @return $this
     */

    public function clearCache()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('welance_kraken/image_cache');
        $query = "DELETE FROM {$table} WHERE 1";

        $writeConnection->query($query);

        return $this;
    }
}