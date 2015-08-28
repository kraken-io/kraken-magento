<?php

class Welance_Kraken_Model_Adminhtml_System_Config_Source_Concurrency
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();

        for ($i = 1; $i <= 10; $i++) {
            $label = $i;

            if ($i == 4) {
                $label .= " (recommended)";
            }

            $optionArray[] = array('value' => $i, 'label' => $label);
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $optionArray = array();

        for ($i = 1; $i <= 10; $i++) {
            $optionArray[$i] = $i;
        }

        return $optionArray;
    }

}