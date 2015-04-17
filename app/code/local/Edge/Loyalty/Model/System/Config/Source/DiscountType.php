<?php

class Edge_Loyalty_Model_System_Config_Source_DiscountType
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'by_percent',
                'label' => Mage::helper('loyalty')->__('By Percentage of the Original Price')
            ),
            array(
                'value' => 'by_fixed',
                'label' => Mage::helper('loyalty')->__('By Fixed Amount')
            ),
            array(
                'value' => 'to_percent',
                'label' => Mage::helper('loyalty')->__('To Percentage of the Original Price')
            ),
            array(
                'value' => 'to_fixed',
                'label' => Mage::helper('loyalty')->__('To Fixed Amount')
            )
        );
    }
}

