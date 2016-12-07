<?php

class Welance_Kraken_Model_Adminhtml_System_Config_Source_Compression
{

    const COMPRESSION_TYPE_LOSSY = 0;
    const COMPRESSION_TYPE_LOSSLESS = 1;
    const COMPRESSION_TYPE_CUSTOM = 2;
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_helper = Mage::helper('welance_kraken');

        return array(
            array('value' => self::COMPRESSION_TYPE_LOSSY, 'label' => $_helper->__('Intelligent lossy (recommended)')),
            array('value' => self::COMPRESSION_TYPE_LOSSLESS, 'label' => $_helper->__('Lossless')),
            array('value' => self::COMPRESSION_TYPE_CUSTOM, 'label' => $_helper->__('Custom'))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $_helper = Mage::helper('welance_kraken');

        return array(
            self::COMPRESSION_TYPE_LOSSY => $_helper->__('Intelligent lossy (recommended)'),
            self::COMPRESSION_TYPE_LOSSLESS => $_helper->__('Lossless'),
            self::COMPRESSION_TYPE_CUSTOM => $_helper->__('Custom')
        );
    }

}
