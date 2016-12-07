<?php

class Welance_Kraken_Block_Adminhtml_Api_User_Status extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $statusCheckButton = $this->_getUserStatusButton();
        return $statusCheckButton;
    }


    private function _getUserStatusButton()
    {
        $statusCheckUrl = $this->getUrl('adminhtml/kraken/userstatus');

        $statusCheckButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__('Check Your Kraken.io Account'))
            ->setOnClick("window.location.href='" . $statusCheckUrl . "'")
            ->toHtml();

        return $statusCheckButton;
    }

}