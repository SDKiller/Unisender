<?php
/**
 * @author RUGENTO
 *
 */
class Rugento_Unisender_Model_Source_Optin
{
    /**
     * @return multitype:multitype:string  multitype:string Ambigous <string, string, multitype:>
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label' => Mage::helper('unisender')->__('Without confirmation')),
            array('value'=>'1', 'label' => Mage::helper('unisender')->__('Confirmation only Guest')),
            array('value'=>'2', 'label' => Mage::helper('unisender')->__('Confirmation for All')),
        );
    }
}