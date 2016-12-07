<?php

class Welance_Kraken_Model_Adminhtml_System_Config_Backend_Validation_Quality extends Mage_Core_Model_Config_Data
{
    public function _beforeSave()
    {
        $valid  = new Zend_Validate_Between(array('min' => 1, 'max' => 100));

        if(!$valid->isValid($this->getValue())){
            $this->_dataSaveAllowed = false;
            Mage::throwException(Mage::helper('welance_kraken')->__('Please use a value between 1 and 100'));
        }

        return parent::_beforeSave();
    }
}