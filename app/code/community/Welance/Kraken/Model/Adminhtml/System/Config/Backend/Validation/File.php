<?php

class Welance_Kraken_Model_Adminhtml_System_Config_Backend_Validation_File extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        $fileExtensions = str_replace('.','',$this->getValue());

        $this->setValue($fileExtensions);

        return parent::save();
    }
}