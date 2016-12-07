<?php

class Welance_Kraken_Block_Adminhtml_Images extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('kraken/images.phtml');
    }
}