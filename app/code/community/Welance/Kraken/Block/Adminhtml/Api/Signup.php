<?php

class Welance_Kraken_Block_Adminhtml_Api_Signup extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $signUpLink = $this->_getSignUpLink();
        return $signUpLink;
    }

    private function _getSignUpLink()
    {
        $html = "<a href='https://kraken.io/plans' target='_blank'>".Mage::helper('welance_kraken')->__('Don\'t have an account? Sign Up.')."</a>";

        return $html;
    }
}