<?php

class Edge_Loyalty_Model_System_Config_Source_PeriodType
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'days',
                'label' => Mage::helper('loyalty')->__('Days')
            ),
            array(
                'value' => 'months',
                'label' => Mage::helper('loyalty')->__('Months')
            ),
            array(
                'value' => 'years', 
                'label' => Mage::helper('loyalty')->__('Years')
            ),
        );
    }
}

